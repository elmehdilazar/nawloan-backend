<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use App\Models\Policy;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PolicyController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware(['permission:policies_create'])->only('create');
        // $this->middleware(['permission:policies_update'])->only('edit');
        // $this->middleware(['permission:policies_enable'])->only('changeStatus');
        // $this->middleware(['permission:policies_disable'])->only('changeStatus');
        // $this->middleware(['permission:policies_export'])->only('export');
    }
    public function index(Request $request){
        if (session('success')) {
            toast(session('success'), 'success');
        }
        if($request->has('quick_search')){
            $policies = $this->quickSearch($request );
            return view('admin.policies.index',$policies);
        }else{
            $policies  = Policy::withTrashed();
            $rowsNumber = $policies->count();
            return view('admin.policies.index',$policies)->paginate(15);
        }
    }

    private function quickSearch(Request $request ){
        $policies = Policy::where('title_en',$request->quick_search)
        ->orWhere('title_ar',$request->quick_search)->withTrashed()->paginate(15)->get();
        return $policies;
    }
    public function create(){
        return redirect()->route('admin.policy.create');
    }
    public function store(PolicyRequest $request){
        $policy = $request.validated();
        $policy["user_id"] = auth()->user()->id ;
        DB::beginTransaction();
        Policy::create($policy);

            $data = [
                'title' =>'add',
                'body' => "add",
                'target' => 'add_policy',
                'link'  => route('admin.policy.index', [ 'policy_en' => $policy['policy_en']]),
                'target_id' => $policy['policy_en'],
                'sender' => auth()->user()->name,
            ];
            $this->sendNotification($data);
            DB::commit();
            session()->flash('success', __('site.added_success'));

            return redirect()->route('admin.policy.index');
    }

    public function edit($id){
        $policy = Policy::withTrashed()->find($id);
        return redirect()->route('admin.policy.edit',$policy);
    }
    public function update(PolicyRequest $request,$id){
        $policy =Policy::withTrashed()->findOrFail($id);
        DB::beginTransaction();
        $request->update($request->validated());

            $data = [
                'title' =>'update',
                'body' => "update",
                'target' => 'update_policy',
                'link'  => route('admin.policy.index', [ 'policy_en' => $policy['policy_en']]),
                'target_id' => $policy['policy_en'],
                'sender' => auth()->user()->name,
            ];
            $this->sendNotification($data);
            DB::commit();
            session()->flash('success', __('site.added_success'));
            return redirect()->route('admin.policy.index');

    }

    public function destory($id){
        Policy::find($id)->delete;
        session()->flash('success', __('site.disable_success'));
        return redirect()->route('admin.policy.index');
    }

    private function sendNotification($data){
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }
    }
}
