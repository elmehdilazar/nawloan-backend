<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ShipmentsExport;
use App\Http\Controllers\Controller;
use App\Models\ShipmentType;
use App\Models\User;
use App\Notifications\LocalNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class ShipmentTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //create read update delete
        $this->middleware(['permission:shipments_types_read'])->only('index');
        $this->middleware(['permission:shipments_types_create'])->only('create');
        $this->middleware(['permission:shipments_types_update'])->only('edit');
        $this->middleware(['permission:shipments_types_enable'])->only('changeStatus');
        $this->middleware(['permission:shipments_types_disable'])->only('changeStatus');
        $this->middleware(['permission:shipments_types_export'])->only('export');
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
            $shipmentTypes = ShipmentType::when($request->name_en, function ($query) use ($request) {
                return $query->where('name_en', 'like', '%' . $request->name_en . '%');
            })->when($request->name_ar, function ($query) use ($request) {
                return $query->where('name_ar', 'like', '%' . $request->name_ar . '%');
            })->select()->inactive()->latest()->orderBy('id', 'desc')->paginate(10);
            return view('admin.shipmentType.index', ['shipmentTypes' => $shipmentTypes]);
        } elseif ($request->active == 2 ) {
            $shipmentTypes = ShipmentType::when($request->name_en, function ($query) use ($request) {
                return $query->where('name_en', 'like', '%' . $request->name_en . '%');
            })->when($request->name_ar, function ($query) use ($request) {
                return $query->where('name_ar', 'like', '%' . $request->name_ar . '%');
            })->select()->active()->latest()->orderBy('id', 'desc')->paginate(10);
            return view('admin.shipmentType.index', ['shipmentTypes' => $shipmentTypes]);
        } elseif ( $request->active ==0) {
            $shipmentTypes = ShipmentType::when($request->name_en, function ($query) use ($request) {
                return $query->where('name_en', 'like', '%' . $request->name_en . '%');
            })->when($request->name_ar, function ($query) use ($request) {
                return $query->where('name_ar', 'like', '%' . $request->name_ar . '%');
            })->select()->latest()->orderBy('id', 'desc')->paginate(10);
            return view('admin.shipmentType.index', ['shipmentTypes' => $shipmentTypes]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.shipmentType.create');
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
            'name_ar'      =>      'required|string|unique:shipment_types,name_ar',
            'name_en'      =>      'required|string|unique:shipment_types,name_en',
        ]);
        if (!$request->has('active')) {
            $request->request->add(['active' => 0]);
        } else {
            $request->request->add(['active' => 1]);
        }
        $shipmentType=ShipmentType::create([
            'name_en'      =>  $request->name_en,
            'name_ar'      =>  $request->name_ar,
            'active'    =>  $request->active
        ]);
        $data = [
            'title' => 'add',
            'body' => 'add_body',
            'target' => 'shipment_type',
            'link'  => route('admin.shipment.index', [ 'name_en' => $shipmentType->name_en]),
            'target_id' => $shipmentType->name_en,
            'sender' => auth()->user()->name,
        ];
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->orWhere('type', 'emp')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }
        session()->flash('success', __('site.added_success'));
        return redirect()->route('admin.shipment.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ShipmentType  $shipmentType
     * @return \Illuminate\Http\Response
     */
    public function show(ShipmentType $shipmentType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ShipmentType  $shipmentType
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $shipment=ShipmentType::find($id);
        if (!$shipment) {
            session()->flash('errors', __('site.shipmentType_not_found'));
            return redirect()->route('admin.shipment.index');
        }
        return view('admin.shipmentType.edit',['shipment'=>$shipment]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ShipmentType  $shipmentType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $shipment = ShipmentType::find($id);
        if (!$shipment) {
            session()->flash('errors', __('site.shipmentType_not_found'));
            return redirect()->route('admin.shipment.index');
        }
        $request->validate([
            'name_ar'      =>      'string',
            'name_ar' => ['required', Rule::unique('shipment_types')->ignore($shipment->id),],
            'name_en'      =>      'string',
            'name_en' => ['required', Rule::unique('shipment_types')->ignore($shipment->id),],
        ]);
        if (!$request->has('active')) {
            $request->request->add(['active' => 0]);
        } else {
            $request->request->add(['active' => 1]);
        }
        DB::beginTransaction();
        $shipment->update([
            'name_ar'      =>  $request->name_ar,
            'name_en'      =>  $request->name_en,
            'active'    =>  $request->active
        ]);
        $data = [
            'title' => 'edit',
            'body' => 'edit_body',
            'target' => 'shipment_type',
            'link'  => route('admin.shipment.index', ['name_en' => $shipment->name_en]),
            'target_id' => $shipment->name_en,
            'sender' => auth()->user()->name,
        ];
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->orWhere('type', 'emp')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }
        DB::commit();
        session()->flash('success', __('site.edited_success'));
        return redirect()->route('admin.shipment.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ShipmentType  $shipmentType
     * @return \Illuminate\Http\Response
     */
    public function destroy(ShipmentType $shipmentType)
    {
        //
    }
    public function changeStatus($id)
    {
        $shipmentType = ShipmentType::select()->find($id);
        if (!$shipmentType) {
            session()->flash('errors', __('site.shipmentType_not_found'));
            return redirect()->route('admin.shipment.index');
        }
        if ($shipmentType->active == 1) {
            $shipmentType->update(['active' => 0]);
            $data = [
                'title' => 'disable',
                'body' => 'disable_body',
                'target' => 'shipment_Type',
                'link'  => route('admin.shipment.index', ['name_en' => $shipmentType->name_en]),
                'target_id' => $shipmentType->name_en,
                'sender' => auth()->user()->name,
            ];
            $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
            foreach ($users as $user1) {
                Notification::send($user1, new LocalNotification($data));
            }
            session()->flash('success', __('site.disable_success'));
        } elseif ($shipmentType->active == 0) {
            $shipmentType->update(['active' => 1]);
            $data = [
                'title' => 'enable',
                'body' => 'enable_body',
                'target' => 'shipment_Type',
                'link'  => route('admin.shipment.index', ['name_en' => $shipmentType->name_en]),
                'target_id' => $shipmentType->name_en,
                'sender' => auth()->user()->name,
            ];
            $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
            foreach ($users as $user1) {
                Notification::send($user1, new LocalNotification($data));
            }
            session()->flash('success', __('site.enable_success'));
        }
        return redirect()->route('admin.shipment.index');
    }

    public function export()
    {
        return Excel::download(new ShipmentsExport,  Lang::get('site.shipments_types') . '-' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx');
    }
}
