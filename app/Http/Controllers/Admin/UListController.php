<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UList;
use App\Models\User;
use App\Models\UListUser;
use App\Notifications\LocalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Notification;

class UListController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // restrict bulk delete to users with disable permission
        $this->middleware(['permission:ulists_disable'])->only('destroySelected');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ulists=UList::latest()->paginate(10);
        return view('admin.ulists.index',['ulists'=>$ulists]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users=User::where('user_type','service_provider')->orWhere('user_type','service_seeker')->get();
        $utypes=$users->groupBy('type');
        return view('admin.ulists.create',['users'=>$users,'utypes'=>$utypes]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_en'   =>  'required|string|unique:u_lists,name_en',
            'name_ar'   =>  'required|string|unique:u_lists,name_ar',
            'users'     =>  'required|min:1|exists:users,id'
            ]);

        DB::beginTransaction();
        $ulist=UList::create([
            'name_en'=> $request->name_en,
            'name_ar'=> $request->name_ar,
            ]);
            foreach($request->users as $uid)
            {
                $ulistu=UListUser::create([
                    'user_id'=>$uid,
                    'u_list_id'=>$ulist->id
                    ]);
            }
        DB::commit();
        session()->flash('success', __('site.added_success'));
        return redirect()->route('admin.ulists.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UList  $uList
     * @return \Illuminate\Http\Response
     */
    public function show(UList $uList)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UList  $uList
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $list=Ulist::find($id);
        if (!$list) {
            session()->flash('errors', __('site.ulist_not_found'));
            return redirect()->route('admin.ulists.index');
        }
        $users=User::where('user_type','service_provider')->orWhere('user_type','service_seeker')->get();
        $utypes=$users->groupBy('type');
        return view('admin.ulists.edit',['list'=>$list,'utypes'=>$utypes]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UList  $uList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $ulist=UList::find($id);
        $request->validate([
            'name_en'   =>  'string',
            'name_en' => ['required', Rule::unique('u_lists')->ignore($ulist->id),],
            'name_ar'   =>  'string',
            'name_ar' => ['required', Rule::unique('u_lists')->ignore($ulist->id),],
            'users'     =>  'required|min:1|exists:users,id'
            ]);

        DB::beginTransaction();
        $ulist->update([
            'name_en'=> $request->name_en,
            'name_ar'=> $request->name_ar,
            ]);
            foreach($request->users as $uid)
            {
                $ulistu=UListUser::UpdateOrCreate([
                    'user_id'=>$uid,
                    'u_list_id'=>$ulist->id
                    ]);
            }
        DB::commit();
        session()->flash('success', __('site.updated_success'));
        return redirect()->route('admin.ulists.index');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UList  $uList
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ulist=UList::find($id);
        DB::beginTransaction();
            foreach($ulist->users as $uid)
            {
                $ulistu=UListUser::where($uid->user->id)->get();
                $ulistu->delete();
            }
        DB::commit();
        session()->flash('success', __('site.updated_success'));
        return redirect()->route('admin.ulists.index');

    }
    public function changeStatus($id)
    {
        $ulist = UList::select()->find($id);
        if (!$ulist) {
            session()->flash('errors', __('site.ulist_not_found'));
            return redirect()->route('admin.ulists.index');
        }
        if ($ulist->active == 1) {
            $ulist->update(['active' => 0]);
            $data = [
                'title' => 'disable',
                'body' => 'disable_body',
                'target' => 'ulist',
                'link'  => route('admin.ulists.index', ['name_en' => $ulist->name_en]),
                'target_id' => $ulist->name_en,
                'sender' => auth()->user()->name,
            ];
            $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
            foreach ($users as $user1) {
                Notification::send($user1, new LocalNotification($data));
            }
            session()->flash('success', __('site.disable_success'));
        } elseif ($ulist->active == 0) {
            $ulist->update(['active' => 1]);
            $data = [
                'title' => 'enable',
                'body' => 'enable_body',
                'target' => 'ulist',
                'link'  => route('admin.ulists.index', ['name_en' => $ulist->name_en]),
                'target_id' => $ulist->name_en,
                'sender' => auth()->user()->name,
            ];
            $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
            foreach ($users as $user1) {
                Notification::send($user1, new LocalNotification($data));
            }
            session()->flash('success', __('site.enable_success'));
        }
        return redirect()->route('admin.ulists.index');
    }
    public function export(){
    //  return Excel::download(new UsersExport,  Lang::get('site.users').'-'.Carbon::now()->format('Y-m-d_H-i-s').'.xlsx');
    }

    public function destroySelected(Request $request)
    {
        $ids = $request->query('ids', []);
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
            // Remove relations then delete lists
            \App\Models\UListUser::whereIn('u_list_id', $ids)->delete();
            $deleted = UList::whereIn('id', $ids)->delete();
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
}
