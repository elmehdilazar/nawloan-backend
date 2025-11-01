<?php

namespace App\Http\Controllers\Admin;

use App\Exports\EnterprisesExport;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Evaluate;
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

class FactoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //create read update delete
        $this->middleware(['permission:factories_read'])->only(['index', 'evaluates']);
        $this->middleware(['permission:factories_create'])->only('create');
        $this->middleware(['permission:factories_update'])->only('edit');
        $this->middleware(['permission:factories_enable'])->only(['changeStatus', 'EvalchangeStatus']);
        $this->middleware(['permission:factories_disable'])->only(['changeStatus', 'EvalchangeStatus']);
        $this->middleware(['permission:factories_export'])->only('export');
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
                return $query->where('phone', $request->phone);
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
            })->select()->inactive()->where('type', 'factory')
                ->latest()->orderBy('id', 'desc')->paginate(10);
            return view('admin.factories.index', ['users' => $users,'ulists'=>$ulists]);
        } elseif ($request->active == 2) {
            $users = User::when($request->name, function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
            })->when($request->phone, function ($query) use ($request) {
                return $query->where('phone', $request->phone);
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
            })->select()->active()->where('type', 'factory')
                ->latest()->orderBy('id', 'desc')->paginate(10);
            return view('admin.factories.index', ['users' => $users,'ulists'=>$ulists]);
        } elseif ($request->active == 0) {
            $users = User::when($request->name, function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
            })->when($request->phone, function ($query) use ($request) {
                return $query->where('phone', $request->phone);
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
            })->select()->where('type', 'factory')->latest()->orderBy('id', 'desc')->paginate(10);
            return view('admin.factories.index', ['users' => $users,'ulists'=>$ulists]);
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('admin.factories.create');
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
            'email'                      =>      'required|email|unique:users,email',
            'phone'                     =>      'required|unique:users,phone|max:20',
            'password'                  =>      'required|confirmed|string|min:4',
            'commercial_record'         =>      'nullable|string|max:255|unique:user_data,commercial_record',
            'tax_card'                  =>      'nullable|string|max:255|unique:user_data,tax_card',
            'longitude'                  =>      'nullable',
            'latitude'                  =>      'nullable',
            'location'                  =>      'nullable',
            'pay_date'                  =>      'required',
            'image'                     =>      'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'commercial_record_image_f' =>      'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'commercial_record_image_b' =>      'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'tax_card_image_f'          =>      'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'tax_card_image_b'          =>      'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'bank_name'                 =>      'nullable|string',
            'branch_name'               =>      'nullable|string',
            'account_holder_name'       =>      'nullable|string',
            'account_number'            =>      'nullable|string',
            'soft_code'                 =>      'nullable|string',
            'iban'                      =>      'nullable|string',
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
        $request_data = $request->except(['password', 'password_confirmation', 'image', 'commercial_record_image_f', 'commercial_record_image_b', 'tax_card_image_f', 'tax_card_image_b']);
        $request_data['password'] = bcrypt($request->password);
        $request_userData['image'] = 'uploads/users/default.png';
        if ($request->image) {
            Image::make($request->image)
                ->save(public_path('uploads/users/' . $request->image->hashName()));
            $request_userData['image'] = 'uploads/users/' . $request->image->hashName();
        }
        $request_userData['commercial_record_image_f'] = null;
        if ($request->commercial_record_image_f) {
            Image::make($request->commercial_record_image_f)
                ->save(public_path('uploads/commercial_records/' . $request->commercial_record_image_f->hashName()));
            $request_userData['commercial_record_image_f'] = 'uploads/commercial_records/' . $request->commercial_record_image_f->hashName();
        }
        $request_userData['commercial_record_image_b'] = null;
        if ($request->commercial_record_image_b) {
            Image::make($request->commercial_record_image_b)
                ->save(public_path('uploads/commercial_records/' . $request->commercial_record_image_b->hashName()));
            $request_userData['commercial_record_image_b'] = 'uploads/commercial_records/' . $request->commercial_record_image_b->hashName();
        }
        $request_userData['tax_card_image_f'] = null;
        if ($request->tax_card_image_f) {
            Image::make($request->tax_card_image_f)
                ->save(public_path('uploads/tax_cards/' . $request->tax_card_image_f->hashName()));
            $request_userData['tax_card_image_f'] = 'uploads/tax_cards/' . $request->tax_card_image_f->hashName();
        }
        $request_userData['tax_card_image_b'] = null;
        if ($request->tax_card_image_b) {
            Image::make($request->tax_card_image_b)
                ->save(public_path('uploads/tax_cards/' . $request->tax_card_image_b->hashName()));
            $request_userData['tax_card_image_b'] = 'uploads/tax_cards/' . $request->tax_card_image_b->hashName();
        }
        DB::beginTransaction();
        $user = User::create([
            'name'      =>  $request_data['name'],
            'phone'     =>  $request_data['phone'],
            'email'     =>  $request_data['email'],
            'password'  =>  $request_data['password'],
            'type'      =>  'factory',
            'user_type' =>  'service_seeker',
            'active'        =>  $request_data['active']
        ]);
        $userData = UserData::create([
            'user_id'                   =>      $user->id,
            'type'                      =>      'factory',
            'image'                     =>      $request_userData['image'],
            'phone'                     =>      $request_data['phone'],
            'commercial_record'         =>      $request->commercial_record,
            'commercial_record_image_f' =>      $request_userData['commercial_record_image_f'],
            'commercial_record_image_b' =>      $request_userData['commercial_record_image_b'],
            'tax_card'                  =>      $request->tax_card,
            'tax_card_image_f'          =>      $request_userData['tax_card_image_f'],
            'tax_card_image_b'          =>      $request_userData['tax_card_image_b'],
            'longitude'                  =>      $request_data['longitude'],
            'latitude'                  =>      $request_data['latitude'],
            'location'                  =>      $request_data['location'],
            'vip'                       =>      $request_data['vip'],
            'date_of_payment'           =>      $request_data['pay_date'],
        ]);

        if ($request_data['bank_name'] != '' && $request_data['branch_name'] != '' && $request_data['account_holder_name'] != '' && $request_data['account_number'] != '') {
            $userBank = BankInfo::create([
                'user_id'                   =>      $user->id,
                'bank_name'                 =>      $request_data['bank_name'],
                'branch_name'               =>      $request_data['branch_name'],
                'account_holder_name'       =>      $request_data['account_holder_name'],
                'account_number'            =>      $request_data['account_number'],
                'soft_code'                 =>      $request_data['soft_code'],
                'iban'                      =>      $request_data['iban'],
            ]);
        }
        $data = [
            'title' => 'add',
            'body' => 'add_body',
            'target' => 'factory',
            'link'  => route('admin.factories.index', ['name' => $user->name]),
            'target_id' => $user->name,
            'sender' => auth()->user()->name,
        ];
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }
        DB::commit();
        session()->flash('success', __('site.added_success'));
        return redirect()->route('admin.factories.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::with(['userData', 'bank'])->find($id);
        if (!$user) {
            session()->flash('errors', __('site.user_not_found'));
            return redirect()->route('admin.factories.index');
        }
        return view('admin.factories.show', ['user' => $user]);
    }

    public function evaluates($id)
    {
        $user = User::with('userData')->where('type', 'factory')->find($id);
        if (!$user) {
            session()->flash('errors', __('site.user_not_found'));
            return redirect()->route('admin.factories.index');
        }

        $evaluates = Evaluate::with(['user.userData'])
            ->where('user_id', $user->id)
            ->latest('created_at')
            ->paginate(10);
        $avg = round((float) Evaluate::where('user_id', $user->id)->avg('rate'), 1);

        return view('admin.factories.evaluate', [
            'user' => $user,
            'evaluates' => $evaluates,
            'avg' => $avg,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::with(['userData', 'bank'])->find($id);
        if (!$user) {
            session()->flash('errors', __('site.user_not_found'));
            return redirect()->route('admin.factories.index');
        }
        return view('admin.factories.edit', ['user' => $user]);
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
            return redirect()->route('admin.factories.index');
        }
        $request->validate([
            'name'                          =>      'string|required',
            'email'                      =>      'required|email',
            'email'                         =>      ['required', Rule::unique('users')->ignore($user->id),],
            'phone'                         =>           'max:20',
            'phone'                         =>      ['required', Rule::unique('users')->ignore($user->id),],
            'commercial_record'             =>      'string|max:255|unique:user_data,commercial_record',
            'commercial_record'             =>      ['required', Rule::unique('user_data', 'user_id')->ignore($user->id),],
            'tax_card'                      =>      'string|max:255|unique:user_data,commercial_record',
            'tax_card'                      =>      ['required', Rule::unique('user_data', 'user_id')->ignore($user->id),],
            'longitude'                      =>      'nullable',
            'latitude'                          =>      'nullable',
            'location'                      =>      'nullable',
            'pay_date'                      =>      'required',
            'image'                         =>      'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'commercial_record_image_f'     =>      'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'commercial_record_image_b'     =>      'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'tax_card_image_f'              =>      'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'tax_card_image_b'              =>      'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'bank_name'                 =>      'nullable|string',
            'branch_name'               =>      'nullable|string',
            'account_holder_name'       =>      'nullable|string',
            'account_number'            =>      'nullable|string',
            'soft_code'                 =>      'nullable|string',
            'iban'                      =>      'nullable|string',
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
        $request_data = $request->except(['image', 'commercial_record_image_f', 'commercial_record_image_b', 'tax_card_image_f', 'tax_card_image_b']);
       // $request_data['password'] = bcrypt($request->password);
        $request_userData['image'] = 'uploads/users/default.png';
        $userData = UserData::where('user_id', $user->id)->get()->first();
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
                'image' => 'uploads/users/' . $request->image->hashName()
            ]);
        }
        if ($request->commercial_record_image_f) {
            if ($user->userData->commercial_record_image_f != '' && $user->userData->commercial_record_image_f != 'uploads/users/default.png') {
                if (file_exists(public_path($user->userData->commercial_record_image_f))) {
                    unlink(public_path($user->userData->commercial_record_image_f));
                }
            }
            Image::make($request->commercial_record_image_f)
                ->save(public_path('uploads/commercial_records/' . $request->commercial_record_image_f->hashName()));
            $request_userData['commercial_record_image_f'] = 'uploads/commercial_records/' . $request->commercial_record_image_f->hashName();
            $userData->update([
                'commercial_record_image_f' => 'uploads/commercial_records/' . $request->commercial_record_image_f->hashName()
            ]);
        }
        if ($request->commercial_record_image_b) {
            if ($user->userData->commercial_record_image_b != '' && $user->userData->commercial_record_image_b != 'uploads/users/default.png') {
                if (file_exists(public_path($user->userData->commercial_record_image_b))) {
                    unlink(public_path($user->userData->commercial_record_image_b));
                }
            }
            Image::make($request->commercial_record_image_b)
                ->save(public_path('uploads/commercial_records/' . $request->commercial_record_image_b->hashName()));
            $request_userData['commercial_record_image_b'] = 'uploads/commercial_records/' . $request->commercial_record_image_b->hashName();
            $userData->update([
                'commercial_record_image_b' => 'uploads/commercial_records/' . $request->commercial_record_image_b->hashName()
            ]);
        }
        if ($request->tax_card_image_f) {
            if ($user->userData->tax_card_image_f != '' && $user->userData->tax_card_image_f != 'uploads/users/default.png') {
                if (file_exists(public_path($user->userData->tax_card_image_f))) {
                    unlink(public_path($user->userData->tax_card_image_f));
                }
            }
            Image::make($request->tax_card_image_f)
                ->save(public_path('uploads/tax_cards/' . $request->tax_card_image_f->hashName()));
            $request_userData['tax_card_image_f'] = 'uploads/tax_cards/' . $request->tax_card_image_f->hashName();
            $userData->update([
                'tax_card_image_f' => 'uploads/tax_cards/' . $request->tax_card_image_f->hashName()
            ]);
        }
        if ($request->tax_card_image_b) {
            if ($user->userData->tax_card_image_b != '' && $user->userData->tax_card_image_b != 'uploads/users/default.png') {
                if (file_exists(public_path($user->tax_card_image_b))) {
                    unlink(public_path($user->userData->tax_card_image_b));
                }
            }
            Image::make($request->tax_card_image_b)
                ->save(public_path('uploads/tax_cards/' . $request->tax_card_image_b->hashName()));
            $request_userData['tax_card_image_b'] = 'uploads/tax_cards/' . $request->tax_card_image_b->hashName();
            $userData->update([
                'tax_card_image_b' => 'uploads/tax_cards/' . $request->tax_card_image_b->hashName()
            ]);
        }
        DB::beginTransaction();
        $user->update([
            'name'      =>  $request_data['name'],
            'phone'     =>  $request_data['phone'],
            'email'     =>  $request_data['email'],
            'active'  =>  $request_data['active'],

        ]);
        if ($request->has('password') && $request->has('password_confirmation')) {
            $request_data['password'] = bcrypt($request->password);
            $user->update([
                'password'  =>   $request_data['password'],
            ]);
        }
        
        $userData->update([
            'type'                      =>      $user->type,
            'phone'                     =>      $user->phone,
            'commercial_record'         =>      $request->commercial_record,
            'tax_card'                  =>      $request->tax_card,
            'longitude'                 =>      $request->longitude,
            'latitude'                  =>      $request->latitude,
            'location'                  =>      $request->location,
            'revision'                  =>      $request->revision,
            'vip'                       =>      $request->vip,
            'date_of_payment'           =>      $request->pay_date,
        ]);
        if (!empty($request_data['bank_name']) && !empty($request_data['branch_name']) && !empty($request_data['account_holder_name']) && !empty($request_data['account_number'])) {
            $userBank = BankInfo::where('user_id', $user->id)->first();
            if (!empty($userBank)) {
                $userBank->update([
                    'user_id'                   =>      $user->id,
                    'bank_name'                 =>      $request_data['bank_name'],
                    'branch_name'               =>      $request_data['branch_name'],
                    'account_holder_name'       =>      $request_data['account_holder_name'],
                    'account_number'            =>      $request_data['account_number'],
                    'soft_code'                 =>      $request_data['soft_code'],
                    'iban'                      =>      $request_data['iban'],
                ]);
            } else {
                $userBank = BankInfo::create([
                    'user_id'                   =>      $user->id,
                    'bank_name'                 =>      $request_data['bank_name'],
                    'branch_name'               =>      $request_data['branch_name'],
                    'account_holder_name'       =>      $request_data['account_holder_name'],
                    'account_number'            =>      $request_data['account_number'],
                    'soft_code'                 =>      $request_data['soft_code'],
                    'iban'                      =>      $request_data['iban'],
                ]);
            }
        }
        $data = [
            'title' => 'edit',
            'body' => 'edit_body',
            'target' => 'factory',
            'link'  => route('admin.factories.index', ['name' => $user->name]),
            'target_id' => $user->name,
            'sender' => auth()->user()->name,
        ];
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }

        DB::commit();
        session()->flash('success', __('site.edited_success'));
        return redirect()->route('admin.factories.index');
    }
    public function changeStatus($id)
    {
        $user = User::select()->find($id);
        if (!$user) {
            session()->flash('errors', __('site.user_not_found'));
            return redirect()->route('admin.factories.index');
        }
        if ($user->active == 1) {
            $user->update(['active' => 0]);
            $data = [
                'title' => 'disable',
                'body' => 'disable_body',
                'target' => 'factory',
                'link'  => route('admin.factories.index', ['name' => $user->name]),
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
                'target' => 'factory',
                'link'  => route('admin.factories.index', ['name' => $user->name]),
                'target_id' => $user->name,
                'sender' => auth()->user()->name,
            ];
            $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
            foreach ($users as $user1) {
                Notification::send($user1, new LocalNotification($data));
            }
            session()->flash('success', __('site.enable_success'));
        }
        return redirect()->route('admin.factories.index');

        try {
        } catch (\Exception $ex) {
            DB::rollBack();
            session()->flash('errors', __('site.error_ocurred'));
            return redirect()->route('admin.factories.index');
        }
    }

    public function EvalchangeStatus($id)
    {
        $evaluate = Evaluate::find($id);
        if (!$evaluate) {
            session()->flash('errors', __('site.evaluate_not_found'));
            return redirect()->back();
        }

        $factory = User::find($evaluate->user_id);
        if (!$factory || $factory->type !== 'factory') {
            session()->flash('errors', __('site.user_not_found'));
            return redirect()->route('admin.factories.index');
        }

        if ((int) $evaluate->active === 1) {
            $evaluate->update(['active' => 0]);
            session()->flash('success', __('site.disable_success'));
        } else {
            $evaluate->update(['active' => 1]);
            session()->flash('success', __('site.enable_success'));
        }

        return redirect()->route('admin.factories.evaluate', ['id' => $evaluate->user_id]);
    }

    public function export()
    {
        return Excel::download(new EnterprisesExport,  Lang::get('site.factories') . '-' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx');
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

        // Soft delete selected factories (users) for safe removal
        User::whereIn('id', $ids)->delete();
        return back()->with('success', __('site.deleted_success'));
    }
}
