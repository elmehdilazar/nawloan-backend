<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ApiExport;
use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Notifications\LocalNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Notification;
use Intervention\Image\Facades\Image;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class GatewayController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', 'is_super_admin']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        if (session('success')) {
            toast(session('success'), 'success');
        }
        if ($request->active == 1) {
            $gateway = PaymentMethod::when($request->name, function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
            })->when($request->type, function ($query) use ($request) {
                return $query->where('desc', $request->type);
            })->select()->inactive()->latest()->orderBy('id', 'desc')->paginate(10);
            return view('admin.gateway.index', ['gateway' => $gateway]);
        } elseif ($request->active == 2) {
            $gateway = PaymentMethod::when($request->name, function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
            })->when($request->type, function ($query) use ($request) {
                return $query->where('desc', $request->type);
            })->select()->active()->latest()->orderBy('id', 'desc')->paginate(10);
            return view('admin.gateway.index', ['gateway' => $gateway]);
        } elseif ($request->active == 0) {
            $gateway = PaymentMethod::when($request->name, function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
            })->when($request->type, function ($query) use ($request) {
                return $query->where('desc', $request->type);
            })->select()->latest()->orderBy('id', 'desc')->paginate(10);
            return view('admin.gateway.index', ['gateway' => $gateway]);
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.gateway.create');
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
            'name' => 'required|string|unique:payment_methods,name',
            'publishable_key' => 'required|string',
            'secret_key' => 'required|string',
            'type' => 'required|string',
            'image' => 'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
        ]);
        if (!$request->has('active')) {
            $request->request->add(['active' => 0]);
        } else {
            $request->request->add(['active' => 1]);
        }
        $request_data = $request->except(['image']);
        // Ensure an image path key always exists
        $request_data['image'] = 'uploads/gateway/default.png';
        if ($request->hasFile('image')) {
            Image::make($request->image)
                ->save(public_path('uploads/gateway/' . $request->image->hashName()));
            $request_data['image'] = 'uploads/gateway/' . $request->image->hashName();
        }
        $gateway = PaymentMethod::create([
            'name'      =>  $request_data['name'],
            'publishable_key'      =>  $request_data['publishable_key'],
            'secret_key'     =>  $request_data['secret_key'],
            'desc'      =>  $request_data['type'],
            'image'      =>  $request_data['image'],
            'active'    =>  $request_data['active']
        ]);
        $data = [
            'title' => 'add',
            'body' => 'add_body',
            'target' => 'gateway',
            'link'  => route('admin.gateway.index', ['name' => $gateway->name]),
            'target_id' => $gateway->name,
            'sender' => auth()->user()->name,
        ];
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }
        session()->flash('success', __('site.added_success'));
        return redirect()->route('admin.gateway.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PaymentMethod  $paymentMethod
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $gateway = PaymentMethod::find($id);
        if (!$gateway) {
            session()->flash('errors', __('site.gateway_not_found'));
            return redirect()->route('admin.gateway.index');
        }
        return view('admin.gateway.edit', ['gateway' => $gateway]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Car  $car
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $gateway = PaymentMethod::find($id);
        if (!$gateway) {
            session()->flash('errors', __('site.car_not_found'));
            return redirect()->route('admin.gateway.index');
        }
        $request->validate([
            'name'      =>       'string',
            'name'      =>       ['required', Rule::unique('payment_methods')->ignore($gateway->id),],
            'publishable_key' => 'required|string',
            'secret_key' => 'required|string',
            'type' => 'required|string',
            'image'     =>      'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
        ]);
        $request_data = $request->except(['image']);
        $request_data['image'] = 'uploads/gateway/default.png';
        if ($request->image) {
            if ($gateway->image != '' && $gateway->image != 'uploads/gateway/default.png') {
                if (file_exists(public_path($gateway->image))) {
                    unlink(public_path($gateway->image));
                }
            }
            Image::make($request->image)
                ->save(public_path('uploads/gateway/' . $request->image->hashName()));
            $request_data['image'] = 'uploads/gateway/' . $request->image->hashName();
        }
        $gateway->update([
            'name'      =>  $request_data['name'],
            'publishable_key'     =>  $request_data['publishable_key'],
            'secret_key'      =>  $request_data['secret_key'],
            'image'     =>  $request_data['image'],
            'desc'      =>  $request_data['type']
        ]);
        $data = [
            'title' => 'edit',
            'body' => 'edit_body',
            'target' => 'gateway',
            'link'  => route('admin.gateway.index', ['name' => $gateway->name]),
            'target_id' => $gateway->name,
            'sender' => auth()->user()->name,
        ];
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }
        session()->flash('success', __('site.edited_success'));
        return redirect()->route('admin.gateway.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PaymentMethod  $paymentMethod
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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

        // Clean up images if necessary
        $items = \App\Models\PaymentMethod::whereIn('id', $ids)->get();
        foreach ($items as $item) {
            if (!empty($item->image) && $item->image !== 'uploads/gateway/default.png') {
                $path = public_path($item->image);
                if (file_exists($path)) {
                    @unlink($path);
                }
            }
        }

        $deleted = \App\Models\PaymentMethod::whereIn('id', $ids)->delete();
        if ($deleted < 1) {
            return back()->with('error', __('site.no_items_selected'));
        }

        return back()->with('success', __('site.deleted_success'));
    }
    public function changeStatus($id)
    {
        $gateway = PaymentMethod::select()->find($id);
        if (!$gateway) {
            session()->flash('errors', __('site.gateway_not_found'));
            return redirect()->route('admin.gateway.index');
        }
        if ($gateway->active == 1) {
            $gateway->update(['active' => 0]);
            $data = [
                'title' => 'disable',
                'body' => 'disable_body',
                'target' => 'gateway',
                'link'  => route('admin.gateway.index', ['name' => $gateway->name]),
                'target_id' => $gateway->name,
                'sender' => auth()->user()->name,
            ];
            $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
            foreach ($users as $user1) {
                Notification::send($user1, new LocalNotification($data));
            }
            session()->flash('success', __('site.disable_success'));
        } elseif ($gateway->active == 0) {
            $gateway->update(['active' => 1]);
            $data = [
                'title' => 'enable',
                'body' => 'enable_body',
                'target' => 'gateway',
                'link'  => route('admin.gateway.index', ['name' => $gateway->name]),
                'target_id' => $gateway->name,
                'sender' => auth()->user()->name,
            ];
            $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
            foreach ($users as $user1) {
                Notification::send($user1, new LocalNotification($data));
            }
            session()->flash('success', __('site.enable_success'));
        }
        return redirect()->route('admin.gateway.index');
    }

    public function export()
    {
        return Excel::download(new ApiExport,  Lang::get('site.external_gateway') . '-' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx');
    }
}
