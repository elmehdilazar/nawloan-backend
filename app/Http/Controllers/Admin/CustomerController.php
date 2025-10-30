<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CustomersExport;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BankInfo;
use App\Models\UserData;

use App\Models\UList;
use App\Notifications\LocalNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        //create read update delete
        $this->middleware(['permission:customers_read'])->only('index');
        $this->middleware(['permission:customers_create'])->only('create');
        $this->middleware(['permission:customers_update'])->only('edit');
        $this->middleware(['permission:customers_enable'])->only('changeStatus');
        $this->middleware(['permission:customers_disable'])->only('changeStatus');
        $this->middleware(['permission:customers_export'])->only('export');
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
        $ulists=UList::select('id','name_en','name_ar')->get();
        if ($request->active == 1) {
            $users = User::when($request->name, function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
            })->when($request->phone, function ($query) use ($request) {
                return $query->where('phone', $request->phone );
            })->when($request->vip, function ($query) use ($request) {
                return $query->whereHas('userData',function ($query) use ($request){
                    return $query->select()->when($request->vip, function ($query1) use ($request) {
                        return $query1->where('vip',  $request->vip);
                    });
                });
            })->when($request->list_name, function ($query) use ($request) {
                return $query->whereHas('ulists',function ($query) use ($request){
                    return $query->select()->when($request->list_name, function ($query1) use ($request) {
                        return $query1->where('u_list_id',  $request->list_name);
                    });
                });
            })->select()->inactive()->where('type', 'user')
            ->latest()->orderBy('id', 'desc')->paginate(10);
            return view('admin.customers.index', ['users' => $users,'ulists'=>$ulists]);
        } elseif ($request->active == 2) {
            $users = User::when($request->name, function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
            })->when($request->phone, function ($query) use ($request) {
                return $query->where('phone', $request->phone );
            })->when($request->vip, function ($query) use ($request) {
                return $query->whereHas('userData',function ($query) use ($request){
                    return $query->select()->when($request->vip, function ($query1) use ($request) {
                        return $query1->where('vip',  $request->vip);
                    });
                });
            })->when($request->list_name, function ($query) use ($request) {
                return $query->whereHas('ulists',function ($query) use ($request){
                    return $query->select()->when($request->list_name, function ($query1) use ($request) {
                        return $query1->where('u_list_id',  $request->list_name);
                    });
                });
            })->select()->active()->where('type', 'user')
            ->latest()->orderBy('id', 'desc')->paginate(10);
            
            return view('admin.customers.index', ['users' => $users,'ulists'=>$ulists]);
        } elseif ($request->active == 0) {
            $users = User::when($request->name, function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
            })->when($request->phone, function ($query) use ($request) {
                return $query->where('phone', $request->phone );
            })->when($request->vip, function ($query) use ($request) {
                return $query->whereHas('userData',function ($query) use ($request){
                    return $query->select()->when($request->vip, function ($query1) use ($request) {
                        return $query1->where('vip',  $request->vip);
                    });
                });
            })->when($request->list_name, function ($query) use ($request) {
                return $query->whereHas('ulists',function ($query) use ($request){
                    return $query->select()->when($request->list_name, function ($query1) use ($request) {
                        return $query1->where('u_list_id',  $request->list_name);
                    });
                });
            })->select()->where('type', 'user')
             ->latest()->orderBy('id', 'desc')->paginate(10);
          
            return view('admin.customers.index', ['users' => $users,'ulists'=>$ulists]);
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('admin.customers.create');
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
            'name'                      =>      'required|string',
            'phone'                     =>      'required|unique:users,phone|max:20',
            'password'                  =>      'required|confirmed|string|min:4',
            'image'                     =>      'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
        ]);

        if (!$request->has('active')) {
            $request->request->add(['active' => 0]);
        } else {
            $request->request->add(['active' => 1]);
        }

        if (!$request->has('vip')) {
            $request->request->add(['vip' => 0]);
        } else {
            $request->request->add(['vip' => 1]);
        }
        
         if (!$request->has('revision')) {
            $request->request->add(['revision' => 0]);
        } else {
            $request->request->add(['revision' => 1]);
        }
        $request_data = $request->except(['password', 'password_confirmation','image',]);
        $request_data['password'] = bcrypt($request->password);
        $request_userData['image']='uploads/users/default.png';
        if ($request->image) {
            Image::make($request->image)
                ->save(public_path('uploads/users/' . $request->image->hashName()));
            $request_userData['image'] = 'uploads/users/' . $request->image->hashName();
        }
        DB::beginTransaction();
        $user = User::create([
            'name'      =>  $request_data['name'],
            'phone'     =>  $request_data['phone'],
            'password'  =>  $request_data['password'],
            'type'      =>  'user',
            'user_type' =>  'service_seeker',
            'active'    =>  $request_data['active']
        ]);
        $userData = UserData::create([
            'user_id'                   =>      $user->id,
            'type'                      =>      'user',
            'image'                     =>      $request_userData['image'],
            'phone'                     =>      $request_data['phone'],
            'vip'                       =>      $request_data['vip']
            ]);
        $data = [
            'title' => 'add',
            'body' => 'add_body',
            'target' => 'customers',
            'link'  => route('admin.customers.index', ['name' => $user->name]),
            'target_id' => $user->name,
            'sender' => auth()->user()->name,
        ];
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }
        DB::commit();
        session()->flash('success', __('site.added_success'));
        return redirect()->route('admin.customers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user=User::with(['userData','bank'])->find($id);
        if (!$user) {
            session()->flash('errors', __('site.user_not_found'));
            return redirect()->route('admin.customers.index');
        }
        return view('admin.customers.show',['user'=>$user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user=User::with(['userData','bank'])->find($id);
        if (!$user) {
            session()->flash('errors', __('site.user_not_found'));
            return redirect()->route('admin.customers.index');
        }
        return view('admin.customers.edit', ['user' => $user]);
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
            return redirect()->route('admin.customers.index');
        }
        $request->validate([
            'name'                          =>      'required|string',
            'phone'                         =>           'max:20',
            'phone'                         =>      ['required', Rule::unique('users')->ignore($user->id),],
            //'password'                  =>      'nullable|confirmed|string|min:4',
            'image'                         =>      'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',

        ]);

        if (!$request->has('active')) {
            $request->request->add(['active' => 0]);
        } else {
            $request->request->add(['active' => 1]);
        }
        if (!$request->has('vip')) {
            $request->request->add(['vip' => 0]);
        } else {
            $request->request->add(['vip' => 1]);
        }
        if (!$request->has('revision')) {
            $request->request->add(['revision' => 0]);
        } else {
            $request->request->add(['revision' => 1]);
        }
        $request_data = $request->except(['password', 'password_confirmation', 'image',]);
        $request_data['password'] = bcrypt($request->password);
        $request_userData['image'] = 'uploads/users/default.png';
        $userData = UserData::where('user_id',$user->id)->get()->first();
        if ($request->image) {
            if ($user->userData->image != '' && $user->userData->image != 'uploads/users/default.png') {
                if (file_exists(public_path($user->userData->image))) {
                    unlink(public_path($user->userData->image));
                }
            }
            Image::make($request->image)
                ->save(public_path('uploads/users/' . $request->image->hashName()));
            $request_userData['image'] = 'uploads/users/' . $request->image->hashName();
             $userData->update([
                'image'=>'uploads/users/' . $request->image->hashName(),
                'phone'=>$request->phone
            ]);
        }
        DB::beginTransaction();
        // if($request->has('password')) && $request->has('password_confirmation'))
        // {
        //   $user->update([
        //         'password'  =>  $request_data['password']
        //     ]);
        // }
        $user->update([
            'name'      =>  $request_data['name'],
            'phone'     =>  $request_data['phone'],
            'active'    =>  $request_data['active']
        ]);
        
        $userData->update(['revision'=>$request->revision]);
        $userData->update(['vip'=>$request->vip]);
        $data = [
            'title' => 'edit',
            'body' => 'edit_body',
            'target' => 'customer',
            'link'  => route('admin.customers.index', ['name' => $user->name]),
            'target_id' => $user->name,
            'sender' => auth()->user()->name,
        ];
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }

        DB::commit();
        session()->flash('success', __('site.edited_success'));
        return redirect()->route('admin.customers.index');
    }
    public function changeStatus($id)
    {
        $user = User::select()->find($id);
        if (!$user) {
            session()->flash('errors', __('site.user_not_found'));
            return redirect()->route('admin.customers.index');
        }
        if ($user->active == 1) {
            $user->update(['active' => 0]);
            $data = [
                'title' => 'disable',
                'body' => 'disable_body',
                'target' => 'customer',
                'link'  => route('admin.customers.index', ['name' => $user->name]),
                'target_id' => $user->name,
                'sender' => auth()->user()->name,
            ];
            $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
            foreach ($users as $user1) {
                Notification::send($user1, new LocalNotification($data));
            }
            session()->flash('success', __('site.disable_success'));
        } elseif ($user->active == 0) {
            $user->update(['active' => 1]);
            $data = [
                'title' => 'enable',
                'body' => 'enable_body',
                'target' => 'customer',
                'link'  => route('admin.customers.index', ['name' => $user->name]),
                'target_id' => $user->name,
                'sender' => auth()->user()->name,
            ];
            $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
            foreach ($users as $user1) {
                Notification::send($user1, new LocalNotification($data));
            }
            session()->flash('success', __('site.enable_success'));
        }
        return redirect()->route('admin.customers.index');

        try {
        } catch (\Exception $ex) {
            DB::rollBack();
            session()->flash('errors', __('site.error_ocurred'));
            return redirect()->route('admin.customers.index');
        }
    }
    public function export()
    {
        return Excel::download(new CustomersExport,  Lang::get('site.customers') . '-' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx');
    }

    public function destroySelected(Request $request)
    {
        $ids = $request->input('ids', $request->input('id', $request->query('ids', [])));
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }
        $ids = array_values(array_unique(array_map('intval', (array)$ids)));
        $ids = array_values(array_filter($ids, fn($id) => $id > 0));

        if (empty($ids)) {
            return back()->with('error', __('site.no_items_selected'));
        }

        // Soft delete selected customers (users)
        User::whereIn('id', $ids)->delete();
        return back()->with('success', __('site.deleted_success'));
    }
}
