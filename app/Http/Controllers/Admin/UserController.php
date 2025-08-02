<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserData;
use App\Notifications\LocalNotification;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Hash;
use App\Rules\MatchOldPassword;
use App\Notifications\FcmPushNotification;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Lang;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //create read update delete
        $this->middleware(['permission:users_read'])->only('index');
        $this->middleware(['permission:users_create'])->only('create');
        $this->middleware(['permission:users_update'])->only('edit');
        $this->middleware(['permission:users_enable'])->only('changeStatus');
        $this->middleware(['permission:users_disable'])->only('changeStatus');
        $this->middleware(['permission:users_export'])->only('export');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (session('success')) {
            toast(session('success'), 'success');
        }
        if ($request->active == 1) {
            $users = User::when($request->name, function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
            })->when($request->phone, function ($query) use ($request) {
                return $query->where('phone', $request->phone );
            })->select()->inactive()->where('user_type','manage')
            ->latest()->orderBy('id', 'desc')->paginate(10);
            return view('admin.users.index', ['users' => $users]);
        } elseif ($request->active == 2) {
            $users = User::when($request->name, function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
            })->when($request->phone, function ($query) use ($request) {
                return $query->where('phone', $request->phone );
            })->select()->active()->where('user_type', 'manage')
            ->latest()->orderBy('id', 'desc')->paginate(10);
            return view('admin.users.index', ['users' => $users]);
        } elseif ($request->active == 0) {
            $users = User::when($request->name, function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
            })->when($request->phone, function ($query) use ($request) {
                return $query->where('phone', $request->phone );
            })->select()->where('user_type', 'manage')->
            latest()->orderBy('id', 'desc')->paginate(10);
            return view('admin.users.index', ['users' => $users]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('admin.users.create');
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
            'name'=>'required|string|unique:users,name',
            'phone'=>'required|unique:users,phone|max:20',
            'password' => 'required|confirmed|string|min:4',
            'permissions' => 'required|min:1',
            'image' => 'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'type'=>'required'
        ]);
        if (!$request->has('active')) {
            $request->request->add(['active' => 0]);
        } else {
            $request->request->add(['active' => 1]);
        }
        $request_data = $request->except(['password', 'password_confirmation', 'permissions', 'image']);
        $request_data['password'] = bcrypt($request->password);
        $request_data['image']='uploads/users/default.png';
        if ($request->image) {
            Image::make($request->image)
                ->save(public_path('uploads/users/' . $request->image->hashName()));
            $request_data['image'] = 'uploads/users/' . $request->image->hashName();
        }
        DB::beginTransaction();
        DB::commit();
        $user = User::create([
            'name'      =>  $request_data['name'],
            'phone'     =>  $request_data['phone'],
            'password'  =>  $request_data['password'],
            'type'      =>  $request_data['type'],
            'user_type' =>  'manage',
            'active'    =>  $request_data['active']
        ]);
        $userData = UserData::create([
            'user_id' => $user->id,
            'type' => $request_data['type'],
            'image' => $request_data['image'],
            'phone'=> $request_data['phone']
            ]);
        ///$user->attachRole($request_data['type']);
        $user->syncPermissions($request->permissions);
         $data = [
            'title' => 'add',
            'body' => 'add_body',
            'target' => 'user',
            'link'  => route('admin.users.index', ['name' => $user->name]),
            'target_id' => $user->name,
            'sender' => auth()->user()->name,
        ];
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
        foreach ($users as $user1) {
            Notification::send($user1, new LocalNotification($data));


        }
        session()->flash('success', __('site.added_success'));
        return redirect()->route('admin.users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user=User::with('userData')->find($id);
        if (!$user) {
            session()->flash('errors', __('site.user_not_found'));
            return redirect()->route('admin.users.index');
        }
        return view('admin.users.show',['user'=>$user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::with('userData')->find($id);
        if (!$user) {
            session()->flash('errors', __('site.user_not_found'));
            return redirect()->route('admin.users.index');
        }
        return view('admin.users.edit', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::with('userData')->find($id);
        if (!$user) {
            session()->flash('errors', __('site.user_not_found'));
            return redirect()->route('admin.users.index');
        }
        $request->validate([
            'name'      =>      'string',
            'name' => ['required', Rule::unique('users')->ignore($user->id),],
            'phone'      =>      'max:20',
            'phone' => ['required', Rule::unique('users')->ignore($user->id),],
            'permissions' => 'required|min:1',
            'image' => 'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'type' => 'required'
        ]);
        if (!$request->has('active')) {
            $request->request->add(['active' => 0]);
        } else {
            $request->request->add(['active' => 1]);
        }
        $request_data = $request->except(['permissions', 'image']);
        $request_data['password'] = bcrypt($request->password);
        $userData =UserData::where('user_id', $user->id)->get()->first();
         $request_userData['image'] = 'uploads/users/default.png';
        // dd($request_data,$request->permissions);
        DB::beginTransaction();
        if ($request->image) {
            if ($user->userData->image != '' && $user->userData->image != 'uploads/users/default.png') {
                if (file_exists(public_path($user->userData->image))) {
                    unlink(public_path($user->userData->image));
                }
            }
            Image::make($request->image)
                ->save(public_path('uploads/users/' . $request->image->hashName()));
            $userData->update([
                'image' => 'uploads/users/' . $request->image->hashName()]);
        }
        $user->update([
            'name'      =>  $request_data['name'],
            'phone'     =>  $request_data['phone'],
            'password'  =>  $request_data['password'],
            'type'      =>  $request_data['type'],
            'active'    =>  $request_data['active']
        ]);
        $userData->update([
            'user_id' => $user->id,
            'type' => $user->type,
            'phone' => $user->phone
        ]);
        //$user->syncPermissions( [],$user->id);
        $user->syncPermissions($request->permissions);
        $data = [
            'title' => 'edit',
            'body' => 'edit_body',
            'target' => 'user',
            'link'  => route('admin.users.index', ['name' => $user->name]),
            'target_id' => $user->name,
            'sender' => auth()->user()->name,
        ];
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
        foreach ($users as $user1) {
            Notification::send($user1, new LocalNotification($data));
        }
        DB::commit();
        session()->flash('success', __('site.edited_success'));
        return redirect()->route('admin.users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
    public function changeStatus($id)
    {
        $user = User::select()->find($id);
        if (!$user) {
            session()->flash('errors', __('site.user_not_found'));
            return redirect()->route('admin.users.index');
        }
        if ($user->active == 1) {
            $user->update(['active' => 0]);
            $message1= Lang::get('site.disable_account_notice_msg');
            $title1 = Lang::get('site.disable_account_notice_title');
            $data = [
                'title' => 'disable',
                'body' => 'disable_body',
                'target' => 'user',
                'link'  => route('admin.users.index', ['name' => $user->name]),
                'target_id' => $user->name,
                'sender' => auth()->user()->name,
            ];
            $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
            foreach ($users as $user1) {
                Notification::send($user1, new LocalNotification($data));
            }
            if($user->fcm_token){
                //Notification::send($admin, new FcmPushNotification($title, $message, [$user->fcm_token]));
            }
            session()->flash('success', __('site.disable_success'));
        } elseif ($user->active == 0) {
            $user->update(['active' => 1]);
        $message1= Lang::get('site.enable_account_notice_msg');
        $title1 = Lang::get('site.enable_account_notice_title');
            $data = [
                'title' => 'enable',
                'body' => 'enable_body',
                'target' => 'user',
                'link'  => route('admin.users.index', ['name' => $user->name]),
                'target_id' => $user->name,
                'sender' => auth()->user()->name,
            ];
            $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
            foreach ($users as $user1) {
                Notification::send($user1, new LocalNotification($data));
            }
            if($user->fcm_token){
               // Notification::send($admin, new FcmPushNotification($title, $message, [$user->fcm_token]));
            }
            session()->flash('success', __('site.enable_success'));
        }
        return redirect()->route('admin.users.index');

        try {
        } catch (\Exception $ex) {
            DB::rollBack();
            session()->flash('errors', __('site.error_ocurred'));
            return redirect()->route('admin.users.index');
        }
    }

    public function showAndRead($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
            return redirect($notification->data['link']);
        }
        return redirect()->back();
    }
    public function MarkAsRead(Request $request)
    {
        if ($request->id) {
            foreach (auth()->user()->unreadNotifications as $notification) {
                if ($notification->id == $request->id) {
                    $notification->markAsRead();
                    return back();
                }
            }
        }
    }
    public function MarkAsRead_all(Request $request)
    {
        $userUnreadNotification = auth()->user()->unreadNotifications;
        if ($userUnreadNotification) {
            $userUnreadNotification->markAsRead();
            return back();
        }
    }
    public function unreadNotifications_count()
    {
        return auth()->user()->unreadNotifications->count();
    }
    public function unreadNotifications()
    {
        foreach (auth()->user()->unreadNotifications as $notification) {
            return $notification->data['title'];
        }
    }
    public function allNotifications()
    {
        $notifications = auth()->user()->notifications()->paginate(10);
        return view('admin.notifications.index', ['notifications' => $notifications]);
    }

    public function accountShow()
    {
        if (session('success')) {
            toast(session('success'), 'success');
        }
        $user = User::select()->find(auth()->user()->id);
        if (!$user) {
            session()->flash('success', __('site.user_not_found'));
            return redirect()->route('admin.index');
        }
        return view('admin.account.index', ['user' => $user]);
    }
    public function accountEdit()
    {
        $user = User::select()->find(auth()->user()->id);
        if (!$user) {
            session()->flash('success', __('site.user_not_found'));
            return redirect()->route('admin.index');
        }
        return view('admin.account.edit', ['user' => $user]);
    }

    public function accountUpdate(Request $request, $id)
    {

        $user = User::select()->find($id);
        if (!$user) {
            session()->flash('success', __('site.user_not_found'));
            return redirect()->route('admin.index');
        }
        $request->validate(
            [
                'name' => 'required|max:190',
                'phone' => 'required|max:20',
                'image' => 'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            ],
            [
                'name.max' => 'الاسم يحب ألا يتجاوز عن 150 حرف.',
                'name.required' => ' الاسم مطلوب',
                'phone.required' => 'رقم الهاتف مطلوب',
                'phone.max' => 'رقم الجوال يجب أن لا يتجاوز عن 20 رقم',
                'image.max' => 'حجم الصورة كبير جداً , يجب أن لا يتجاوز حجم الصورة عن 10 ميقابايب',
                'image.mimes' => 'إمتداد الصورة غير مدعوم , الإمتدادات المدعومة : jpeg , jpg , png , gif',
                'image.mimetypes' => 'إمتداد الصورة غير مدعوم , الإمتدادات المدعومة : jpeg , jpg , png , gif'
            ]
        );
        $request_data = $request->except(['image']);


        if ($request->image) {
            if ($user->userData->image != 'uploads/users/default.png') {
                if (file_exists(public_path($user->userData->image))) {
                    unlink(public_path($user->userData->image));
                }
            } //end of inner if
            Image::make($request->image)
                ->save(public_path('uploads/users/' . $request->image->hashName()));
            $user->userData->update([
                'image'      =>  'uploads/users/' . $request->image->hashName()]);
        }  //end of if


        DB::beginTransaction();
        $user->update([
            'name'       =>  $request_data['name'],
            'phone'      =>  $request_data['phone'],
        ]);
        if(auth()->user()->type != 'superadministrator'){
            $data = [
                'title' => 'edit_profile',
                'body' => 'edit_body',
                'link'  => route('admin.users.index', ['name' => $user->name]),
                'target' => 'user',
                'target_id' => $user->name,
                'sender' => auth()->user()->name,
            ];
            $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->orWhere('type', 'emp')->get();
            foreach ($users as $user) {
                Notification::send($user, new LocalNotification($data));
            }
        }
        DB::commit();
        session()->flash('success', __('site.account_edit_success'));
        return redirect()->route('admin.account.show');
    }
    public function changePassword()
    {
        return view('admin.account.changePassword');
    }
    public function storePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);
        User::find(auth()->user()->id)->update(['password' => Hash::make($request->new_password)]);
        session()->flash('success', __('site.password_change_success'));
        return redirect()->route('admin.account.show');
    }
    public function export(){
      return Excel::download(new UsersExport,  Lang::get('site.users').'-'.Carbon::now()->format('Y-m-d_H-i-s').'.xlsx');
    }
}
