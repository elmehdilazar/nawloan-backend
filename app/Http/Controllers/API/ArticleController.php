<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\File;
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
use Illuminate\Support\Facades\Storage;


class ArticleController extends Controller
{
    public function __construct()
    {
         $this->middleware('auth')->except(['index', 'show']);
    }

    public function index (Request $request){
        try {
            $categories = article_category::select('id','category_ar','category_en')->get();

            if($request->has('quick_search')){
                $articles = $this->quickSearch($request) ;
                return response()->json([$articles,$categories]);
            }
            $articles = Article::latest()->get();
            foreach ($articles as $key ) {
               $images = $key->images;
               $category = $key->category ;
               $user = $key->user;
            }
            return response()->json([$articles,$categories]);
        } catch (\Excaption $th) {
            return response()->json('Error: '.$th);
           }
    }
    private function quickSearch(Request $request ){
        try {
            $articles = Article::where('article_en','like','%'.$request->quick_search.'%')
            ->orWhere('article_date','like','%'.$request->quick_search.'%')
            ->orWhere('article_ar','like','%'.$request->quick_search.'%')
            ->orWhere('article_desc_en','like','%'.$request->quick_search.'%')
            ->orWhere('article_desc_ar','like','%'.$request->quick_search.'%')
            ->orWhere('meta_title_en','like','%'.$request->quick_search.'%')
            ->orWhere('meta_title_ar','like','%'.$request->quick_search.'%')
            ->orWhere('tage_en','like','%'.$request->quick_search.'%')
            ->orWhere('tage_ar','like','%'.$request->quick_search.'%')
            ->orWhere('meta_desc_en','like','%'.$request->quick_search.'%')
            ->orWhere('meta_desc_ar','like','%'.$request->quick_search.'%')
            ->orWhere('id','like','%'.$request->quick_search.'%')->latest()
            ->get();
            foreach ($articles as $key ) {
                $images = $key->images;
             }
            return $articles;
        } catch (\Excaption $th) {
            return response()->json('Error: '.$th);
           }
    }

    public function indixOnlyTrashed(){
        try {
            $articles = Article::onlyTrashed();
            return response()->json($articles);
        } catch (\Excaption $th) {
            return response()->json('Error: '.$th);
           }
    }

    public function indixWithTrashed(){
        try {
            $articles = Article::withTrashed()->paginate(15);
            return response()->json($articles);
        } catch (\Excaption $th) {
            return response()->json('Error: '.$th);
           }
    }
    public function getcategories(){
        $categories = article_category::select('id','category_ar','category_en')->get();
        
         return response()->json($categories);
    }
    public function store(ArticleRequest $request){
       try {
        DB::beginTransaction();
        $article = $request->validated();
        $article['user_id'] = auth()->user()->id;
        $imagesName=[];
        $fileName;
        if ($request->has('images')) {
            for ($i=0; $i < count($article['images']); $i++) {
                $fileExtension = $article['images'][$i]->getClientOriginalName();
                $fileName = time().'.'.$fileExtension ;
                 $article['images'][$i]->storeAs('articles',$fileName,'articles');
                $imagesName[$fileName]=$fileName;
            }
            Article::create($article);
            $newArticle =Article::latest()->first();
            for ($i=0; $i < count($imagesName); $i++) {
                Article_image::create([
                    'article_id' => $newArticle->id ,
                    'name' => $imagesName[ $fileName] ,
                ]);
            }
        }else{ Article::create($article);}
        $data = [
            'title' =>'Add',
            'body' => "Add ",
            'target' => 'article',
            'link'  => route('admin.articles.index', [ 'article_en' => $article['article_en']]),
            'target_id' => $article['article_en'],
            'sender' => auth()->user()->name,
            ];
            $this->sendNotification($data);
            DB::commit();
            return response()->json('success');
        } catch (\Excaption $th) {
            return response()->json('Error: '.$th);
       }
    }


    public function update(ArticleRequest $request , $id){
        try {
            $article = Article::withTrashed()->findOrFail($id);
            if (auth()->user()->id !=$article->user_id && auth()->user()->type != 'admin'  && auth()->user()->type !='superadministrator' && auth()->user()->type != 'emp') {
                return response()->json('action is denied');
            }
            DB::beginTransaction();
            $article->update($request->validated());
                $data = [
                    'title' =>'Update',
                    'body' => "Update",
                    'target' => 'article',
                    'link'  => route('admin.articles.index', [ 'article_en' => $article['article_en']]),
                    'target_id' => $article['article_en'],
                    'sender' => auth()->user()->name,
                ];
                $this->sendNotification($data);
                DB::commit();
                return response()->json('success');
        } catch (\Throwable $th) {
            return response()->json('Error: ');
        }
    }
    public function destroy($id){
        try {
            $article = Article::findOrFail($id);
            if (auth()->user()->id !=$article->user_id && auth()->user()->type != 'admin'  && auth()->user()->type !='superadministrator' && auth()->user()->type != 'emp') {
                return response()->json('action is denied ');
            }
            if ($article) {
                DB::beginTransaction();
                $article->delete();
                    $data = [
                        'title' =>'disable',
                        'body' => "disable",
                        'target' => 'article',
                        'link'  => route('admin.articles.index', [ 'article_en' => $article['article_en']]),
                        'target_id' =>$article['article_en'],
                        'sender' => auth()->user()->name,
                    ];
                    $this->sendNotification($data);
                    DB::commit();
                    return response()->json('success');
            }
        } catch (\Excaption $th) {
            return response()->json('Error: '.$th);
           }

    }

    public function restore($id){
        try {
            $article = Article::onlyTrashed()->findOrFail($id);
            if (auth()->user()->type != 'admin'  && auth()->user()->type !='superadministrator' && auth()->user()->type != 'emp') {
                return response()->json('action is denied ' );
            }
            if ($article) {

                DB::beginTransaction();
                $article->restore();
                    $data = [
                        'title' =>'Restore',
                        'body' => "Restore",
                        'target' => 'article ',
                        'link'  => route('admin.articles.index', [ 'article_en' => $article['article_en']]),
                        'target_id' =>$article['article_en'],
                        'sender' => auth()->user()->name,
                    ];
                    $this->sendNotification($data);
                    DB::commit();
                    return response()->json('success');

            }

        } catch (\Excaption $th) {
            return response()->json('Error: '.$th);
           }
    }

    public function addimg(Article_imageRequest $request){
       try {
        $article = Article::find($request->article_id);
        if (auth()->user()->id !=$article->id && auth()->user()->type != 'admin'  && auth()->user()->type !='superadministrator' && auth()->user()->type != 'emp') {
            return response()->json('action is denied ');
        }
        $imagesName=[];
        $fileName;
            for ($i=0; $i < count($request['images']); $i++) {
                $fileExtension = $request['images'][$i]->getClientOriginalName();
                $fileName = time().'.'.$fileExtension ;
                $request['images'][$i]->storeAs('articles',$fileName,'articles');
                $imagesName[$fileName]=$fileName;
            }
            for ($i=0; $i < count($imagesName); $i++) {
                Article_image::create([
                    'article_id' => $request->article_id ,
                    'name' => $imagesName[ $fileName] ,
                ]);
            }
            return response()->json('success');
        } catch (\Excaption $th) {
            return response()->json('Error: '.$th);
           }
    }

    public function removeimg($id){
        try {
            $image = article_image::findOrFail($id);
            $fileName = $image->name;
            $filePath = public_path('images/articles'.$fileName);
           if(\File::exists(public_path('images/articles/'.$filePath))) {
                // return('ok');
              \File::delete(public_path('images/articles/'.$filePath));
            }
            $image->delete();
            
            return response()->json('success');
        } catch (\Excaption $th) {
            return response()->json('Error: '.$th);
           }
    }
    private function sendNotification($data){
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }
    }


}
