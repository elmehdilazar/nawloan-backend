<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ArticleCategoryExport;
use App\Exports\CouponExport;
use Maatwebsite\Excel\Facades\Excel;
use Redirect;
use Illuminate\Translation\Translator;
use App\Http\Controllers\Controller;
use App\Models\Article_category;
use App\Models\User;
use App\Notifications\LocalNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use App\Http\Requests\Article_CategoryRequest;


class Article_categoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // create read update delete
        $this->middleware(['permission:articlecategories_read'])->only('index');
        $this->middleware(['permission:articlecategories_create'])->only('create');
        $this->middleware(['permission:articlecategories_update'])->only('edit');
        $this->middleware(['permission:articlecategories_enable'])->only('changeStatus');
        $this->middleware(['permission:articlecategories_disable'])->only('changeStatus');
        $this->middleware(['permission:articlecategories_export'])->only('export');
    }

    public function index (Request $request){
        if (session('success')) {
            toast(session('success'), 'success');
        }
        $categories = Article_Category::when($request->category_en, function ($query) use ($request) {
            return $query->where('category_en', 'like', '%' . $request->category_en . '%');
        })->when($request->category_ar, function ($query) use ($request) {
            return $query->where('category_ar', 'like', '%' . $request->category_ar . '%');
        })->select()->latest()->paginate(10);
        return view('admin.article_categories.index',['categories'=> $categories]);
    }

    public function create(){
        if (session('success')) {
            toast(session('success'), 'success');
        }
        return view('admin.article_categories.create');
    }

    public function store(Article_CategoryRequest $request){
        DB::beginTransaction();
        $categories = $request->validated();
        $categories ['category_en'] = $request->category_en;
        $categories ['category_ar'] = $request->category_ar;
        $categories ['category_desc_en'] = $request->category_desc_en;
        $categories ['category_desc_ar'] = $request->category_desc_ar;
        $categories ['meta_title_en'] = $request->meta_title_en;
        $categories ['meta_title_ar'] = $request->meta_title_ar;
        $categories ['slug_en'] = $request->slug_en;
        $categories ['slug_ar'] = $request->slug_ar;
        $categories ['meta_desc_en'] = $request->meta_desc_en;
        $categories ['meta_desc_ar'] = $request->meta_desc_ar;

        Article_Category::create($categories);

            $data = [
                'title' =>'Add',
                'body' => "Add Category",
                'target' => 'article category',
                'link'  => route('admin.article_categories.index', [ 'category_en' => $categories['category_en']]),
                'target_id' => $categories['category_en'],
                'sender' => auth()->user()->name,
            ];
            $this->sendNotification($data);
            DB::commit();
            session()->flash('success', __('site.added_success'));

            return redirect()->route('admin.article_categories.index');

        Article_Category::create($request->validated());
    }

    public function edit($id){
        if (session('success')) {
            toast(session('success'), 'success');
        }
        $category = Article_Category::withTrashed()->find($id);
        if ($category) {
            return view('admin.article_categories.edit',['category'=>$category]);
        }
        session()->flash('errors', __('site.server_error'));
        return route('admin.article_categories.index');
    }

    public function update(Article_CategoryRequest $request,$id){
        $category = Article_Category::withTrashed()->findOrFail($id);
        DB::beginTransaction();
        $category->update($request->validated());

            $data = [
                'title' =>'Update',
                'body' => "Update Category",
                'target' => 'article category',
                'link'  => route('admin.article_categories.index', [ 'name_en' => $category['category_en']]),
                'target_id' => $category['category_en'],
                'sender' => auth()->user()->name,
            ];
            $this->sendNotification($data);
            DB::commit();
            session()->flash('success', __('site.edited_success'));

            return redirect()->route('admin.article_categories.index');


    }
    public function destroy($id){
        $category = Article_Category::findOrFail($id);
        if ($category) {
            DB::beginTransaction();
            $category->delete();
                $data = [
                    'title' =>'disable',
                    'body' => "disable",
                    'target' => 'article category',
                    'link'  => route('admin.article_categories.index', [ 'name_en' => $category['category_en']]),
                    'target_id' =>$category['category_en'],
                    'sender' => auth()->user()->name,
                ];
                $this->sendNotification($data);
                DB::commit();
                session()->flash('success', __('site.disable_success'));

                return redirect()->route('admin.article_categories.index');
        }

    }

    public function restore($id){
        $category = Article_Category::onlyTrashed()->findOrFail($id);
        if ($category) {
            DB::beginTransaction();
            $category->restore();

                $data = [
                    'title' =>'Restore',
                    'body' => "Restore",
                    'target' => 'article category',
                    'link'  => route('admin.article_categories.index', [ 'name_en' => $category['category_en']]),
                    'target_id' =>$category['category_en'],
                    'sender' => auth()->user()->name,
                ];
                $this->sendNotification($data);
                DB::commit();
                session()->flash('success', __('site.enable_success'));
                return redirect()->route('admin.article_categories.index');

        }

    }


    private function sendNotification($data){
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }
    }


    private function indixOnlyTrashed(){
        $categories = Article_Category::onlyTrashed()->paginate;
    }

    public function changeStatus($id)
    {
        $article_category = Article_Category::select()->find($id);
        if (!$article_category) {
            session()->flash('errors', __('site.Article Category not found'));
            return redirect()->route('admin.article_categories.index');
        }
        if ($article_category->active == 1) {
            $article_category->active = 0;
            $article_category->save();
            $data = [
                'title' => 'disable',
                'body' => 'disable_body',
                'target' => 'country',
                'link'  => route('admin.article_categories.index', ['name' => $article_category->category_en]),
                'target_id' => $article_category->category_en,
                'sender' => auth()->user()->name,
            ];

            $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
            foreach ($users as $user1) {
                Notification::send($user1, new LocalNotification($data));
            }
            session()->flash('success', __('site.disable_success'));
        } elseif ($article_category->active == 0) {
            $article_category->active = 1;
            $article_category->save();
            $data = [
                'title' => 'enable',
                'body' => 'enable_body',
                'target' => 'country',
                'link'  => route('admin.countries.index', ['name' => $article_category->category_en]),
                'target_id' => $article_category->category_en,
                'sender' => auth()->user()->name,
            ];
            $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
            foreach ($users as $user1) {
                Notification::send($user1, new LocalNotification($data));
            }
            session()->flash('success', __('site.enable_success'));
        }
        return redirect()->route('admin.article_categories.index');
    }

    public function export()
    {

        return Excel::download(new ArticleCategoryExport,  Lang::get('site.articles') . '-' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx');
    }
}
