<?php

namespace App\Http\Controllers\Admin;

use File ;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Translation\Translator;
use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use App\Models\Article_image;
use App\Http\Requests\Article_imageRequest;
use App\Models\Article_category;
use App\Models\User;
use App\Notifications\LocalNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // create read update delete
        $this->middleware(['permission:articles_read'])->only('index');
        $this->middleware(['permission:articles_create'])->only('create');
        $this->middleware(['permission:articles_update'])->only('edit');
        $this->middleware(['permission:articles_enable'])->only('changeStatus');
        $this->middleware(['permission:articles_disable'])->only('changeStatus');
        $this->middleware(['permission:articles_export'])->only('export');
        $this->middleware(['permission:articles_disable'])->only('destroySelected');
    }

    public function index (Request $request){
        if (session('success')) {
            toast(session('success'), 'success');
        }
        $categories =$this->getcategories();
        if($request->has('quick_search')){
            $articles = Article::all();
            return view('admin.articles.index',['articles'=> $articles,'categories'=>$categories]);
        }
        $articles = Article::when($request->article_en, function ($query) use ($request) {
            return $query->where('article_en', 'like', '%' . $request->article_en . '%');
        })->select()->latest()->paginate(10);

        $article_category = Article_category::all();
        foreach ($articles as $key ) {
           $images = $key->images;
        }

        return view('admin.articles.index',['articles'=> $articles,'article_category'=>$article_category]);
    }

    public function getcategories(){
        $categories =Article_category::all();
        return $categories;
    }

    private function quickSearch(Request $request ){
        $articles = Article::where('article_en',$request->quick_search)
        ->orWhere('article_ar','like','%'.$request->quick_search.'%')
        ->orWhere('article_date','like','%'.$request->quick_search.'%')
        ->orWhere('article_desc_en','like','%'.$request->quick_search.'%')
        ->orWhere('article_desc_ar','like','%'.$request->quick_search.'%')
        ->orWhere('meta_title_en','like','%'.$request->quick_search.'%')
        ->orWhere('meta_title_ar','like','%'.$request->quick_search.'%')
        ->orWhere('tage_en','like','%'.$request->quick_search.'%')
        ->orWhere('tage_ar','like','%'.$request->quick_search.'%')
        ->orWhere('meta_desc_en','like','%'.$request->quick_search.'%')
        ->orWhere('meta_desc_ar','like','%'.$request->quick_search.'%')
        ->orWhere('id','like','%'.$request->quick_search.'%')
        ->get();
        foreach ($articles as $key ) {
            $images = $key->images;
         }
        return $articles;
    }

    public function indixOnlyTrashed(){
        $articles = Article::onlyTrashed()->paginate(15);
        return view('admin.articles.index',['articles'=> $articles]);
    }

    public function indixWithoutTrashed(){
        $articles = Article::paginate(15);
        return view('admin.articles.index',['articles'=> $articles]);
    }

    public function create(){
        $categories = Article_category::all();
        return view('admin.articles.create',['categories'=>$categories]);
    }

    public function store(ArticleRequest $request){

        DB::beginTransaction();
        $articles = $request->validated();
        $articles ['user_id'] = auth()->user()->id;
        $articles ['article_date'] = $request->article_date;
        $articles ['article_en'] = $request->article_en;
        $articles ['article_ar'] = $request->article_ar;
        $articles ['article_desc_en'] = $request->article_desc_en;
        $articles ['article_desc_ar'] = $request->article_desc_ar;
        $articles ['meta_title_en'] = $request->meta_title_en;
        $articles ['meta_title_ar'] = $request->meta_title_ar;
        $articles ['tage_en'] = $request->tage_en;
        $articles ['tage_ar'] = $request->tage_ar;
        $articles ['meta_desc_en'] = $request->meta_desc_en;
        $articles ['meta_desc_ar'] = $request->meta_desc_ar;
        $articles['images']        = $request->images ;


        $imagesName=[];
        $fileName;
        if ($request->has('images')) {
            for ($i=0; $i < count($articles['images']); $i++) {
                $fileExtension = $articles['images'][$i]->getClientOriginalName();
                $fileName = time().'.'.$fileExtension ;
                $path = 'public/images/articles';
                $articles['images'][$i]->move($path, $fileName);
                $imagesName[$fileName]=$fileName;
            }
            Article::create($articles);
            $newArticle =Article::latest()->first();
            for ($i=0; $i < count($imagesName); $i++) {
                Article_image::create([
                    'article_id' => $newArticle->id ,
                    'name' => $imagesName[ $fileName] ,
                ]);
            }
        }else{ Article::create($articles);}
            $data = [
                'title' =>'add',
                'body' => "add",
                'target' => 'article',
                'link'  => route('admin.articles.index', [ 'article_en' => $articles['article_en']]),
                'target_id' => $articles['article_en'],
                'sender' => auth()->user()->name,
            ];
            $this->sendNotification($data);
            DB::commit();
            session()->flash('success', __('site.added_success'));
            return redirect()->route('admin.articles.index');
    }

    public function edit($id){
        if (session('success')) {
            toast(session('success'), 'success');
        }
        $articles = Article::withTrashed()->find($id);
        $categories = Article_category::all();
        $article_images = Article_image::query()->where('article_id',$id)->get();
        if ($articles) {
            return view('admin.articles.edit',['articles'=>$articles, 'categories'=>$categories,'article_images'=>$article_images]);
        }
        session()->flash('errors', __('site.server_error'));
        return route('admin.articles.index');
    }

    public function update(ArticleRequest $request,$id){
        $articles = Article::withTrashed()->findOrFail($id);

        DB::beginTransaction();
        if ($request->has('images')) {
            for ($i=0; $i < count($request->images); $i++) {
                $fileExtension = $request->images[$i]->getClientOriginalName();
                $fileName = time().'.'.$fileExtension ;
                $path = 'public/images/articles';
                $request->images[$i]->move($path, $fileName);
                $imagesName[$fileName]=$fileName;
            }
            $articles->update($request->validated());
            for ($i=0; $i < count($imagesName); $i++) {
                Article_image::create([
                    'article_id' => $articles->id ,
                    'name' => $imagesName[ $fileName] ,
                ]);
            }
        }else{ $articles->update($request->validated());}

            $data = [
                'title' =>'Update',
                'body' => "Update ",
                'target' => 'article ',
                'link'  => route('admin.articles.index', [ 'article_en' => $articles['article_en']]),
                'target_id' => $articles['article_en'],
                'sender' => auth()->user()->name,
            ];
            $this->sendNotification($data);
            DB::commit();

            session()->flash('success', __('site.edited_success'));
            return redirect()->route('admin.articles.index');
    }

    public function destroy($id){
        $articles = Article::findOrFail($id);
        if ($articles) {
            DB::beginTransaction();
            $articles->delete();
                $data = [
                    'title' =>'disable',
                    'body' => "disable",
                    'target' => 'article articles',
                    'link'  => route('admin.articles.index', [ 'name_en' => $articles['article_en']]),
                    'target_id' =>$articles['article_en'],
                    'sender' => auth()->user()->name,
                ];
                $this->sendNotification($data);
                DB::commit();

                session()->flash('success', __('site.disable_success'));

                return redirect()->route('admin.articles.index');
        }

    }

    public function restore($id){
        $articles = Article::onlyTrashed()->findOrFail($id);
        if ($articles) {
            DB::beginTransaction();
            $articles->restore();

                $data = [
                    'title' =>'enable',
                    'body' => "enabe",
                    'target' => 'article ',
                    'link'  => route('admin.articles.index', [ 'name_en' => $articles['article_en']]),
                    'target_id' =>$articles['article_en'],
                    'sender' => auth()->user()->name,
                ];
                $this->sendNotification($data);
                DB::commit();
                session()->flash('success', __('site.enable_success'));
                return redirect()->route('admin.articles.index');
        }
    }

    public function destroySelected(Request $request)
    {
        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }
        $ids = array_values(array_unique(array_map('intval', (array)$ids)));
        $ids = array_values(array_filter($ids, function ($id) { return $id > 0; }));

        if (empty($ids)) {
            return back()->with('error', __('site.no_items_selected'));
        }

        DB::beginTransaction();
        try {
            $deleted = Article::whereIn('id', $ids)->delete();
            DB::commit();
            if ($deleted < 1) {
                return back()->with('error', __('site.no_items_selected'));
            }
            return back()->with('success', __('site.deleted_success'));
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', __('site.something_wrong'));
        }
    }

    public function addimg(article_imageRequest $request){

         $article = Article::find($request->article_id);
         if (auth()->user()->id !=$article->id && auth()->user()->type != 'admin'  && auth()->user()->type !='superadministrator' && auth()->user()->type != 'emp') {
             return response()->json('action is denied ');
         }
         $imagesName=[];
         $fileName;
             for ($i=0; $i < count($request['images']); $i++) {
                 $fileExtension = $request['images'][$i]->getClientOriginalName();
                 $fileName = time().'.'.$fileExtension ;
                 $path = 'images/articles';
                 $request['images'][$i]->move($path, $fileName);
                 $imagesName[$fileName]=$fileName;
             }
             for ($i=0; $i < count($imagesName); $i++) {
                 Article_image::create([
                     'article_id' => $request->article_id ,
                     'name' => $imagesName[ $fileName] ,
                 ]);
             }
             return $this->edit($request->article_id);

    }

    public function removeimg($id){

             $image = article_image::findOrFail($id);
             $articleId = $image->article_id;
             $fileName = $image->name;
             $filePath = public_path('images/articles'.$fileName);
             if(File::exists($filePath)) {
               File::delete($filePath);
             }
             $image->delete();
            return redirect()->route('admin.articles.edit',$articleId);

    }

    private function sendNotification($data){
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }
    }

    public function changeStatus($id)
    {
        $article = Article::select()->find($id);
        if (!$article) {
            session()->flash('errors', __('site.Article Category not found'));
            return redirect()->route('admin.article_categories.index');
        }
        if ($article->active == 1) {
            $article->active = 0;
            $article->save();
            $data = [
                'title' => 'disable',
                'body' => 'disable_body',
                'target' => 'country',
                'link'  => route('admin.article_categories.index', ['name' => $article->category_en]),
                'target_id' => $article->category_en,
                'sender' => auth()->user()->name,
            ];

            $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
            foreach ($users as $user1) {
                Notification::send($user1, new LocalNotification($data));
            }
            session()->flash('success', __('site.disable_success'));
        } elseif ($article->active == 0) {
            $article->active = 1;
            $article->save();
            $data = [
                'title' => 'enable',
                'body' => 'enable_body',
                'target' => 'country',
                'link'  => route('admin.countries.index', ['name' => $article->category_en]),
                'target_id' => $article->category_en,
                'sender' => auth()->user()->name,
            ];
            $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
            foreach ($users as $user1) {
                Notification::send($user1, new LocalNotification($data));
            }
            session()->flash('success', __('site.enable_success'));
        }
        return redirect()->route('admin.articles.index');
    }



}
