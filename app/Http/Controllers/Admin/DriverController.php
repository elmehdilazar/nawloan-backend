<?php

namespace App\Http\Controllers\Admin;

use App\Exports\DriversExport;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\User;
use App\Models\UList;
use App\Models\Evaluate;
use App\Models\UserData;
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

class DriverController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        //create read update delete
        $this->middleware(['permission:drivers_read'])->only('index');
        $this->middleware(['permission:drivers_create'])->only('create');
        $this->middleware(['permission:drivers_update'])->only('edit');
        $this->middleware(['permission:drivers_enable'])->only('changeStatus');
        $this->middleware(['permission:drivers_disable'])->only('changeStatus');
        $this->middleware(['permission:drivers_export'])->only('export');
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
        $companies = User::select('id', 'name')->where('type', 'driverCompany')->get();
        $ulists=UList::select('id','name_en','name_ar')->get();
        $cars=Car::select('id','name_en','name_ar')->get();
        if ($request->active == 1) {
            $users = User::when($request->name, function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
            })->when($request->online_drivers, function ($query) use ($request) {
                return $query->where('online', $request->online_drivers);
            })->when($request->phone, function ($query) use ($request) {
                return $query->where('phone', $request->phone );
            })->when($request->company_id, function ($query) use ($request) {
                return $query->whereHas('userData',function ($query) use ($request){
                    return $query->select()->when($request->company_id, function ($query1) use ($request) {
                        return $query1->where('company_id',  $request->company_id);
                    });
                });
            })->when($request->track_type, function ($query) use ($request) {
                return $query->whereHas('userData',function ($query) use ($request){
                    return $query->select()->when($request->track_type, function ($query1) use ($request) {
                        return $query1->where('track_type',  $request->track_type);
                    });
                });
            })->when($request->list_name, function ($query) use ($request) {
                return $query->whereHas('ulists',function ($query) use ($request){
                    return $query->select()->when($request->list_name, function ($query1) use ($request) {
                        return $query1->where('u_list_id',  $request->list_name);
                    });
                });
            })->when($request->vip, function ($query) use ($request) {
                return $query->whereHas('userData',function ($query) use ($request){
                    return $query->select()->when($request->vip, function ($query1) use ($request) {
                        return $query1->where('vip',  $request->vip);
                    });
                });
            })->select()->inactive()->where('type','driver')
            ->latest()->orderBy('id', 'desc')->paginate(10);
            return view('admin.drivers.index', ['users' => $users,'companies'=>$companies,'cars'=>$cars,'ulists'=>$ulists]);
        } elseif ($request->active == 2) {
            $users = User::when($request->name, function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
            })->when($request->online_drivers, function ($query) use ($request) {
                return $query->where('online', $request->online_drivers);
            })->when($request->phone, function ($query) use ($request) {
                return $query->where('phone', $request->phone );
            })->when($request->vip, function ($query) use ($request) {
                return $query->whereHas('userData',function ($query) use ($request){
                    return $query->select()->when($request->vip, function ($query1) use ($request) {
                        return $query1->where('vip',  $request->vip);
                    });
                });
            })->when($request->track_type, function ($query) use ($request) {
                return $query->whereHas('userData',function ($query) use ($request){
                    return $query->select()->when($request->track_type, function ($query1) use ($request) {
                        return $query1->where('track_type',  $request->track_type);
                    });
                });
            })->when($request->list_name, function ($query) use ($request) {
                return $query->whereHas('ulists',function ($query) use ($request){
                    return $query->select()->when($request->list_name, function ($query1) use ($request) {
                        return $query1->where('u_list_id',  $request->list_name);
                    });
                });
            })->when($request->company_id, function ($query) use ($request) {
                return $query->whereHas('userData',function ($query) use ($request){
                    return $query->select()->when($request->company_id, function ($query1) use ($request) {
                        return $query1->where('company_id',  $request->company_id);
                    });
                });
            })->select()->active()->where('type', 'driver')
                ->latest()->orderBy('id', 'desc')->paginate(10);
            return view('admin.drivers.index', ['users' => $users,'companies'=>$companies,'cars'=>$cars,'ulists'=>$ulists]);
        } elseif ($request->active == 0) {
            $users = User::when($request->name, function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
            })->when($request->phone, function ($query) use ($request) {
                return $query->where('phone', $request->phone );
            })->when($request->online_drivers, function ($query) use ($request) {
                return $query->where('online', $request->online_drivers);
            })->when($request->vip, function ($query) use ($request) {
                return $query->whereHas('userData',function ($query) use ($request){
                    return $query->select()->when($request->vip, function ($query1) use ($request) {
                        return $query1->where('vip',  $request->vip);
                    });
                });
            })->when($request->track_type, function ($query) use ($request) {
                return $query->whereHas('userData',function ($query) use ($request){
                    return $query->select()->when($request->track_type, function ($query1) use ($request) {
                        return $query1->where('track_type',  $request->track_type);
                    });
                });
            })->when($request->list_name, function ($query) use ($request) {
                return $query->whereHas('ulists',function ($query) use ($request){
                    return $query->select()->when($request->list_name, function ($query1) use ($request) {
                        return $query1->where('u_list_id',  $request->list_name);
                    });
                });
            })->when($request->company_id, function ($query) use ($request) {
                return $query->whereHas('userData',function ($query) use ($request){
                    return $query->select()->when($request->company_id, function ($query1) use ($request) {
                        return $query1->where('company_id',  $request->company_id);
                    });
                });
            })->select()->where('type', 'driver')->
            latest()->orderBy('id', 'desc')->paginate(10);
            return view('admin.drivers.index', ['users' => $users,'companies'=>$companies,'cars'=>$cars,'ulists'=>$ulists]);
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $cars=Car::get();
        $companies = User::select('id', 'name')->where('type', 'driverCompany')->get();
        return view('admin.drivers.create',['cars'=>$cars, 'companies'=> $companies]);
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
            'national_id'               =>      'required|string|max:255|unique:user_data,national_id',
            'car_id'                =>      'required|exists:cars,id',
            'image'                     =>      'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'national_id_image_f'       =>      'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'national_id_image_b'       =>      'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
           'driving_license_number'    =>      'nullable|unique:user_data,driving_license_number',
            'track_license_number'      =>      'required|unique:user_data,track_license_number',
            'track_number'              =>      'required|unique:user_data,track_number',
            'company_id'                =>      'nullable|exists:users,id',
        ]);
        if (!$request->has('active')) {
            $request->request->add(['active' => 0]);
        } else {
            $request->request->add(['active' => 1]);
        }
        if (!$request->has('revision')) {
            $request->request->add(['revision' => 0]);
        } else {
            $request->request->add(['revision' => 1]);
        }
        if (!$request->has('vip')) {
            $request->request->add(['vip' => 0]);
        } else {
            $request->request->add(['vip' => 1]);
        }
        $request_data = $request->except(['password', 'password_confirmation','image','national_id_image_f','national_id_image_b','commercial_record_image_f','commercial_record_image_b','tax_card_image_f','tax_card_image_b']);
        $request_data['password'] = bcrypt($request->password);
        $request_userData['image']='uploads/users/default.png';
        if ($request->image) {
            Image::make($request->image)
                ->save(public_path('uploads/users/' . $request->image->hashName()));
            $request_userData['image'] = 'uploads/users/' . $request->image->hashName();
        }
        $request_userData['national_id_image_f'] = null;
        if ($request->national_id_image_f) {
            Image::make($request->national_id_image_f)
                ->save(public_path('uploads/national_ids/' . $request->national_id_image_f->hashName()));
            $request_userData['national_id_image_f'] = 'uploads/national_ids/' . $request->national_id_image_f->hashName();
        }
        $request_userData['national_id_image_b'] =null;
        if ($request->national_id_image_b) {
            Image::make($request->national_id_image_b)
                ->save(public_path('uploads/national_ids/' . $request->national_id_image_b->hashName()));
            $request_userData['national_id_image_b'] = 'uploads/national_ids/' . $request->national_id_image_b->hashName();
        }
        DB::beginTransaction();
        DB::commit();
        $user = User::create([
            'name'      =>  $request_data['name'],
            'phone'     =>  $request_data['phone'],
                'password'  =>  $request_data['password'],
                'type'=>'driver',
                'user_type'=>'service_provider',
                'active'=>$request_data['active']
        ]);
        $userData = UserData::create([
            'user_id'                   =>      $user->id,
            'type'                      =>      'driver',
            'image'                     =>      $request_userData['image'],
            'phone'                     =>      $request_data['phone'],
            'national_id'               =>      $request_data['national_id'],
            'national_id_image_f'       =>      $request_userData['national_id_image_f'],
            'national_id_image_b'       =>      $request_userData['national_id_image_b'],
            'driving_license_number'    =>      $request_data['driving_license_number'],
            'track_license_number'      =>      $request_data['track_license_number'],
            'track_number'              =>      $request_data['track_number'],
            'track_type'                =>      $request_data['car_id'],
            'revision'                  =>      $request_data['revision'],
            'vip'                       =>      $request_data['vip'],
            ]);

        if ($request->has('company_id')) {
            $userData->update([
                'company_id'                =>      $request->company_id,
            ]);
        }
        $data = [
            'title' => 'add',
            'body' => 'add_body',
            'target' => 'driver',
            'link'  => route('admin.drivers.index', ['name' => $user->name]),
            'target_id' => $user->name,
            'sender' => auth()->user()->name,
        ];
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }
        session()->flash('success', __('site.added_success'));
        return redirect()->route('admin.drivers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user=User::with(['userData'])->find($id);
        if (!$user) {
            session()->flash('errors', __('site.user_not_found'));
            return redirect()->route('admin.drivers.index');
        }
        return view('admin.drivers.show',['user'=>$user]);
    }
    public function evaluates($id){
        $user=User::find($id);
        if (!$user) {
            session()->flash('errors', __('site.user_not_found'));
            return redirect()->route('admin.drivers.index');
        }
        $evaluates=Evaluate::with(['user'])->where('user_id',$user->id)->paginate(10);
        $avg= Evaluate::with(['user'])->where('user_id',$user->id)->avg('rate');
        return view('admin.drivers.evaluate',['user'=>$user,'evaluates'=>$evaluates,'avg'=> $avg]);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user=User::with(['userData'])->find($id);
        if (!$user) {
            session()->flash('errors', __('site.user_not_found'));
            return redirect()->route('admin.drivers.index');
        }
        $cars=Car::select('id','name_ar','name_en')->get();
        $companies = User::select('id', 'name')->where('type', 'driverCompany')->get();
        return view('admin.drivers.edit', ['user' => $user,'cars'=>$cars,'companies'=> $companies]);
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
            return redirect()->route('admin.drivers.index');
        }
        $request->validate([
            'name'                      =>      'required|string',
            'phone'                         =>           'max:20',
            'phone'                         =>      ['nullable', Rule::unique('users')->ignore($user->id),],
            'password'                      =>      'nullable|confirmed|string|min:4',
            'national_id'                   =>      'string|max:255|unique:user_data,national_id',
            'national_id'                   =>      ['nullable', Rule::unique('user_data','user_id')->ignore($user->id),],
            'car_id'                  =>      'nullable|exists:cars,id',
            'track_license_number'          =>      'string|max:255|unique:user_data,track_license_number',
            'track_license_number'          =>      ['nullable', Rule::unique('user_data','user_id')->ignore($user->id),],
            'track_number'                  =>      'string|max:255|unique:user_data,track_number',
            'track_number'                  =>      ['nullable', Rule::unique('user_data','user_id')->ignore($user->id),],
            'company_id'                    =>      'nullable|exists:users,id',
            'driving_license_number'        =>      'string|max:255|unique:user_data,driving_license_number',
            'driving_license_number'        =>      ['nullable', Rule::unique('user_data','user_id')->ignore($user->id),],
            'image'                         =>      'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'national_id_image_f'           =>      'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'national_id_image_b'           =>      'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
        ]);
        if (!$request->has('active')) {
            $request->request->add(['active' => 0]);
        } else {
            $request->request->add(['active' => 1]);
        }
        if (!$request->has('revision')) {
            $request->request->add(['revision' => 0]);
        } else {
            $request->request->add(['revision' => 1]);
        }
        if (!$request->has('vip')) {
            $request->request->add(['vip' => 0]);
        } else {
            $request->request->add(['vip' => 1]);
        }
        $request_data = $request->except(['image', 'national_id_image_f', 'national_id_image_b']);
        if ($request->password != null){ // add if by mohammed
            $request_data['password'] = bcrypt($request->password);
        }else{
            $request_data['password'] = $user->password;
        }        $request_userData['image'] = 'uploads/users/default.png';
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
                'image'=>'uploads/users/' . $request->image->hashName()
            ]);
        }
        if ($request->national_id_image_f) {
            if ($user->userData->national_id_image_f != '' && $user->userData->national_id_image_f != 'uploads/users/default.png') {
                if (file_exists(public_path($user->userData->national_id_image_f))) {
                    unlink(public_path($user->userData->national_id_image_f));
                }
            }
            Image::make($request->national_id_image_f)
                ->save(public_path('uploads/national_ids/' . $request->national_id_image_f->hashName()));
            $userData->update([
                'national_id_image_f'=>'uploads/national_ids/' . $request->national_id_image_f->hashName()
            ]);
        }
        if ($request->national_id_image_b) {
            if ($user->userData->national_id_image_b != '' && $user->userData->national_id_image_b != 'uploads/users/default.png') {
                if (file_exists(public_path($user->national_id_image_b))) {
                    unlink(public_path($user->userData->national_id_image_b));
                }
            }
            Image::make($request->national_id_image_b)
                ->save(public_path('uploads/national_ids/' . $request->national_id_image_b->hashName()));
            $request_userData['national_id_image_b'] = 'uploads/national_ids/' . $request->national_id_image_b->hashName();
            $userData->update([
                'national_id_image_b'=>'uploads/national_ids/' . $request->national_id_image_b->hashName()
            ]);
        }
        DB::beginTransaction();
        $user->update([
            'name'      =>  $request_data['name'],
            'phone'     =>  $request_data['phone'],
            'active'    =>  $request_data['active']
        ]);
        if ($request->has('password') && $request->has('password_confirmation')) {
            $user->update([
                'password'  =>  $request_data['password'],
            ]);
        }
            $userData->update([
            'type'                      =>      $user->type,
            'phone'                     =>      $user->phone,
            'national_id'               =>      $request->national_id,
            'track_type'                =>      $request->car_id,
            'driving_license_number'    =>      $request->driving_license_number,
            'track_number'              =>      $request->track_number,
            'track_license_number'      =>      $request->track_license_number,
            'revision'                  =>      $request->revision,
            'vip'                       =>      $request->vip
        ]);

        if($request->has('company_id')){
            $userData->update([
            'company_id'                =>      $request->company_id,
            ]);
        }
        $data = [
            'title' => 'edit',
            'body' => 'edit_body',
            'target' => 'driver',
            'link'  => route('admin.drivers.index', ['name' => $user->name]),
            'target_id' => $user->name,
            'sender' => auth()->user()->name,
        ];
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }
        DB::commit();

        session()->flash('success', __('site.edited_success'));
        return redirect()->route('admin.drivers.index');
    }
    public function changeStatus($id)
    {
        $user = User::select()->find($id);
        if (!$user) {
            session()->flash('errors', __('site.user_not_found'));
            return redirect()->route('admin.drivers.index');
        }
        if ($user->active == 1) {
            $user->update(['active' => 0]);
            $data = [
                'title' => 'disable',
                'body' => 'disable_body',
                'target' => 'driver',
                'link'  => route('admin.drivers.index', ['name' => $user->name]),
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
                'target' => 'driver',
                'link'  => route('admin.drivers.index', ['name' => $user->name]),
                'target_id' => $user->name,
                'sender' => auth()->user()->name,
            ];
            $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
            foreach ($users as $user1) {
                Notification::send($user1, new LocalNotification($data));
            }
            session()->flash('success', __('site.enable_success'));
        }
        return redirect()->route('admin.drivers.index');

        try {
        } catch (\Exception $ex) {
            DB::rollBack();
            session()->flash('errors', __('site.error_ocurred'));
            return redirect()->route('admin.drivers.index');
        }
    }

    public function EvalchangeStatus($id)
    {
        $evaluates = Evaluate::select()->find($id);
        if (!$evaluates) {
            session()->flash('errors', __('site.evaluate_not_found'));
            return redirect()->back();
        }
        if ($evaluates->active == 1) {
            $evaluates->update(['active' => 0]);
            session()->flash('success', __('site.disable_success'));
        } elseif ($evaluates->active == 0) {
            $evaluates->update(['active' => 1]);
            session()->flash('success', __('site.enable_success'));
        }
        return redirect()->route('admin.drivers.evaluate',['id'=> $evaluates->user_id]);
    }
    public function export()
    {
        return Excel::download(new DriversExport,  Lang::get('site.drivers') . '-' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx');
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

        // Soft delete selected drivers (users)
        User::whereIn('id', $ids)->delete();
        return back()->with('success', __('site.deleted_success'));
    }
}
