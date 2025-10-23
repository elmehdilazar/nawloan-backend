<?php
namespace App\Http\Controllers\Admin;

use Redirect;
use Illuminate\Translation\Translator;
use App\Http\Controllers\Controller;
use App\Models\Career_category;
use App\Models\User;
use App\Notifications\LocalNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use App\Http\Requests\Career_categoryRequest;


class Career_categoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // create read update delete
        $this->middleware(['permission:careercategories_read'])->only('index');
        $this->middleware(['permission:careercategories_create'])->only('create');
        $this->middleware(['permission:careercategories_update'])->only('edit');
        $this->middleware(['permission:careercategories_enable'])->only('changeStatus');
        $this->middleware(['permission:careercategories_disable'])->only('changeStatus');
        $this->middleware(['permission:careercategories_export'])->only('export');
        $this->middleware(['permission:careercategories_disable'])->only('destroySelected');
    }

    public function index (){
        if (session('success')) {
            toast(session('success'), 'success');
        }
        $categories = Career_Category::withTrashed()->paginate(15);
        return view('admin.career_categories.index',['categories'=> $categories]);
    }

    public function create(){
        if (session('success')) {
            toast(session('success'), 'success');
        }
        return view('admin.career_categories.create');
    }

    public function store(Career_categoryRequest $request){

        DB::beginTransaction();
        $categories = $request->validated();

        Career_Category::create($categories);

            $data = [
                'title' =>'add',
                'body' => "create_Category",
                'target' => 'career_category',
                'link'  => route('admin.career_categories.index', [ 'category_en' => $categories['category_en']]),
                'target_id' => $categories['category_en'],
                'sender' => auth()->user()->name,
            ];
            $this->sendNotification($data);
            DB::commit();
            session()->flash('success', __('site.added_success'));

            return redirect()->route('admin.career_categories.index');

        Career_Category::create($request->validated());
    }

    public function edit($id){
        if (session('success')) {
            toast(session('success'), 'success');
        }
        $category = Career_category::withTrashed()->find($id);
        if ($category) {
            return view('admin.career_categories.edit',['category'=>$category]);
        }
        session()->flash('errors', __('site.server_error'));
        return route('admin.career_categories.index');
    }

    public function update(Career_categoryRequest $request,$id){
        $category = Career_category::withTrashed()->findOrFail($id);
        DB::beginTransaction();
        $category->update($request->validated());

            $data = [
                'title' =>'update',
                'body' => "Update",
                'target' => 'career_category',
                'link'  => route('admin.career_categories.index', [ 'name_en' => $category['category_en']]),
                'target_id' => $category['category_en'],
                'sender' => auth()->user()->name,
            ];
            $this->sendNotification($data);
            DB::commit();
            session()->flash('success', __('site.edited_success'));

            return redirect()->route('admin.career_categories.index');


    }
    public function destroy($id){
        $category = Career_category::findOrFail($id);
        if ($category) {
            DB::beginTransaction();
            $category->delete();
                $data = [
                    'title' =>'disable',
                    'body' => "disable",
                    'target' => 'career category',
                    'link'  => route('admin.career_categories.index', [ 'name_en' => $category['category_en']]),
                    'target_id' =>$category['category_en'],
                    'sender' => auth()->user()->name,
                ];
                $this->sendNotification($data);
                DB::commit();
                session()->flash('success', __('site.disable_success'));

                return redirect()->route('admin.career_categories.index');
        }

    }

    public function restore($id){
        $category = Career_category::onlyTrashed()->findOrFail($id);
        if ($category) {
            DB::beginTransaction();
            $category->restore();

                $data = [
                    'title' =>'enable',
                    'body' => "enable",
                    'target' => 'career_category',
                    'link'  => route('admin.career_categories.index', [ 'name_en' => $category['category_en']]),
                    'target_id' =>$category['category_en'],
                    'sender' => auth()->user()->name,
                ];
                $this->sendNotification($data);
                DB::commit();
                session()->flash('success', __('site.enable_success'));
                return redirect()->route('admin.career_categories.index');

        }

    }


    private function sendNotification($data){
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }
    }


    private function indixOnlyTrashed(){
        $categories = Career_Category::onlyTrashed()->paginate;
    }


    public function destroySelected(Request $request)
    {
        $ids = $request->input('ids', $request->query('ids', []));
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }
        $ids = array_values(array_unique(array_map('intval', (array)$ids)));
        $ids = array_values(array_filter($ids, function ($id) { return $id > 0; }));

        if (empty($ids)) {
            return back()->with('error', __('site.no_items_selected'));
        }

        // Soft delete selected categories
        $deleted = Career_category::whereIn('id', $ids)->delete();
        if ($deleted < 1) {
            return back()->with('error', __('site.no_items_selected'));
        }

        return back()->with('success', __('site.deleted_success'));
    }
}
