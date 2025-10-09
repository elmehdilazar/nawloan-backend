<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CountriesCodesExport;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\User;
use App\Notifications\LocalNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;

class CountryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //create read update delete
        $this->middleware(['permission:countries_read'])->only('index');
        $this->middleware(['permission:countries_create'])->only('create');
        $this->middleware(['permission:countries_update'])->only('edit');
        $this->middleware(['permission:countries_enable'])->only('changeStatus');
        $this->middleware(['permission:countries_disable'])->only('changeStatus');
        $this->middleware(['permission:countries_export'])->only('export');
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
            $countries = Country::when($request->name, function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
            })->when($request->phone_code, function ($query) use ($request) {
                return $query->where('phone_code', 'like', '%' . $request->phone_code . '%');
            })->select()->inactive()->latest()->paginate(10);
            return view('admin.countries.index', ['countries' => $countries]);
        } elseif ($request->active == 2) {
            $countries = Country::when($request->name, function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
            })->when($request->phone_code, function ($query) use ($request) {
                return $query->where('phone_code', 'like', '%' . $request->phone_code . '%');
            })->select()->active()->latest()->paginate(10);
            return view('admin.countries.index', ['countries' => $countries]);
        } elseif ($request->active == 0) {
            $countries = Country::when($request->name, function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
            })->when($request->phone_code, function ($query) use ($request) {
                return $query->where('phone_code', 'like', '%' . $request->phone_code . '%');
            })->select()->latest()->paginate(10);
            return view('admin.countries.index', ['countries' => $countries]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.countries.create');
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
            'name' => 'required|string|unique:countries,name',
            'phone_code' => 'required',
            'country_code'=>'required|unique:countries,country_code',
            'image' => 'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
        ]);
        $request_data = $request->except(['image']);
        $request_data['image'] = '';
        if ($request->image) {
            Image::make($request->image)
                ->save(public_path('uploads/countries/' . $request->image->hashName()));
            $request_data['image'] = 'uploads/countries/' . $request->image->hashName();
        }
        $country = Country::create([
            'name'          =>  $request_data['name'],
            'phone_code'    =>  $request_data['phone_code'],
            'country_code'    =>  $request_data['country_code'],
            'image'         =>  $request_data['image'],
        ]);
        $data = [
            'title' => 'add',
            'body' => 'add_body',
            'target' => 'country',
            'link'  => route('admin.countries.index', ['name' => $country->name]),
            'target_id' => $country->name,
            'sender' => auth()->user()->name,
        ];
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }
        session()->flash('success', __('site.added_success'));
        return redirect()->route('admin.countries.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function show(Country $country)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $country = Country::find($id);
        if (!$country) {
            session()->flash('errors', __('site.country_not_found'));
            return redirect()->route('admin.countries.index');
        }
        return view('admin.countries.edit', ['country' => $country]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $country = Country::find($id);
        if (!$country) {
            session()->flash('errors', __('site.country_not_found'));
            return redirect()->route('admin.countries.index');
        }
        $request->validate([
            'name'   =>       'string',
            'name'   =>       ['required', Rule::unique('countries')->ignore($country->id),],
            'phone_code'   =>       'string',
            'phone_code'   =>       ['required', Rule::unique('countries')->ignore($country->id),],
            'image'     =>      'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
        ]);
        $request_data = $request->except(['image']);
        $request_data['image'] ='';
        if ($request->image) {
            if ($country->image != '' && $country->image != 'uploads/countries/default.png') {
                if (file_exists(public_path($country->image))) {
                    unlink(public_path($country->image));
                }
            }
            Image::make($request->image)
                ->save(public_path('uploads/countries/' . $request->image->hashName()));
            $request_data['image'] = 'uploads/countries/' . $request->image->hashName();
        }
        $country->update([
            'name'      =>  $request_data['name'],
            'phone_code'   =>  $request_data['phone_code'],
            'image'     =>  $request_data['image'],
        ]);
        $data = [
            'title' => 'edit',
            'body' => 'edit_body',
            'target' => 'country',
            'link'  => route('admin.countries.index', ['name' => $country->name]),
            'target_id' => $country->name,
            'sender' => auth()->user()->name,
        ];
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }
        session()->flash('success', __('site.edited_success'));
        return redirect()->route('admin.countries.index');
    }

    public function changeStatus($id)
    {
        $country = Country::select()->find($id);
        if (!$country) {
            session()->flash('errors', __('site.country_not_found'));
            return redirect()->route('admin.countries.index');
        }
        if ($country->active == 1) {
            $country->update(['active' => 0]);
            $data = [
                'title' => 'disable',
                'body' => 'disable_body',
                'target' => 'country',
                'link'  => route('admin.countries.index', ['name' => $country->name]),
                'target_id' => $country->name,
                'sender' => auth()->user()->name,
            ];
            $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
            foreach ($users as $user1) {
                Notification::send($user1, new LocalNotification($data));
            }
            session()->flash('success', __('site.disable_success'));
        } elseif ($country->active == 0) {
            $country->update(['active' => 1]);
            $data = [
                'title' => 'enable',
                'body' => 'enable_body',
                'target' => 'country',
                'link'  => route('admin.countries.index', ['name' => $country->name]),
                'target_id' => $country->name,
                'sender' => auth()->user()->name,
            ];
            $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
            foreach ($users as $user1) {
                Notification::send($user1, new LocalNotification($data));
            }
            session()->flash('success', __('site.enable_success'));
        }
        return redirect()->route('admin.countries.index');
    }
    public function export()
    {
        return Excel::download(new CountriesCodesExport,  Lang::get('site.countries_codes') . '-' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx');
    }
}
