<?php

namespace App\Http\Controllers\Admin;

use App\Exports\OrderExport;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\ChatRoom;
use App\Models\Offer;
use App\Models\OfferStatus;
use App\Models\Order;
use App\Models\OrderAccountant;
use App\Models\OrderStatus;
use App\Models\PaymentMethod;
use App\Models\ShipmentType;
use App\Models\User;
use App\Notifications\FcmPushNotification;
use App\Notifications\LocalNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Kutia\Larafirebase\Facades\Larafirebase;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //create read update delete
        $this->middleware(['permission:orders_read'])->only('index');
        $this->middleware(['permission:orders_create'])->only('create');
        $this->middleware(['permission:orders_update'])->only('edit');
        $this->middleware(['permission:orders_enable'])->only('changeStatus');
        $this->middleware(['permission:orders_disable'])->only('changeStatus');
        $this->middleware(['permission:orders_export'])->only('export');
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
        $users=User::select('id','name','type')->where('user_type','service_seeker')->get();
        $cars = Car::select('id', 'name_en','name_ar' )->get();
        $shipments = ShipmentType::select('id', 'name_en', 'name_ar')->get();
            $orders = Order::when($request->number, function ($query) use ($request) {
                return $query->where('id',  $request->number);
            })->when($request->user_id, function ($query) use ($request) {
                return $query->where('user_id', $request->user_id);
            })->when($request->car_id, function ($query) use ($request) {
                    return $query->where('car_id', $request->car_id );
            })->when($request->shipment_type_id, function ($query) use ($request) {
                return $query->where('shipment_type_id', $request->shipment_type_id);
            })->when($request->size, function ($query) use ($request) {
                return $query->where('size', $request->size);
            })->when($request->ton_price, function ($query) use ($request) {
                return $query->where('ton_price', $request->ton_price);
            })->when($request->weight_ton, function ($query) use ($request) {
                return $query->where('weight_ton', $request->weight_ton);
            })->when($request->status, function ($query) use ($request) {
                return $query->where('status', $request->status);
            })->with([
                'car',
                'user.userData',
                'shipmentType',
                'evaluate',
                'paymentType',
                'transaction',
                'accountant',
                'offer.user.userData',
                'offers.user.userData.car',
                'offers.driver.userData.car',
                'serviceProvider.userData',
                'statuses'
            ])->select()->latest()->orderBy('id', 'desc')->paginate(10);
            return view('admin.orders.index', ['orders' => $orders,'users'=>$users,'cars'=>$cars, 'shipments'=> $shipments]);
    }
    public function pend(){
        if (session('success')) {
            toast(session('success'), 'success');
        }
        $orders = Order::with(['car', 'user', 'shipmentType',  'evaluate'])->where('status','pend')->orWhere('status','pending')->select()->latest()->orderBy('id', 'desc')->paginate(10);
        return view('admin.orders.pend', ['orders' => $orders]);
    }
    public function progress(){
        if (session('success')) {
            toast(session('success'), 'success');
        }
        $orders = Order::with(['car', 'user', 'shipmentType',  'evaluate'])->where('status','approve')->orWhere('status','pick_up')->orWhere('status','delivered')->select()->latest()->orderBy('id', 'desc')->paginate(10);
        return view('admin.orders.progress', ['orders' => $orders]);
    }
    public function complete(){
        if (session('success')) {
            toast(session('success'), 'success');
        }
        $orders = Order::with(['car', 'user', 'shipmentType',  'evaluate'])->where('status','complete')->select()->latest()->orderBy('id', 'desc')->paginate(10);
        return view('admin.orders.complete', ['orders' => $orders]);
    }
    public function cancel(){
        if (session('success')) {
            toast(session('success'), 'success');
        }
        $orders = Order::with(['car', 'user', 'shipmentType',  'evaluate'])->where('status','cancel')->select()->latest()->orderBy('id', 'desc')->paginate(10);
        return view('admin.orders.cancel', ['orders' => $orders]);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\order  $order
     * @return \Illuminate\Http\Response
     */
  public function show($id)
{
    $order = Order::with([
        'car',
        'user.userData',        // Corrected nested relationship
        'shipmentType',
        'evaluate',
        'serviceProvider.userData', // Also add this if you're accessing serviceProvider's userData
        'offer.user.userData'       // And this if you're looping offers and accessing nested user.userData
    ])->find($id);

    if (!$order) {
        session()->flash('errors', __('site.order_not_found'));
        return redirect()->route('admin.orders.index');
    }

    $offers = Offer::where('order_id', $order->id)->latest()->paginate(4, ['*'], 'offers');
    $statuses = OrderStatus::latest()->paginate(4, ['*'], 'status');

    $initialMarkers = [
        [
            'position' => ['lat' => 28.625485, 'lng' => 79.821091],
            'label' => ['color' => 'white', 'text' => 'P1'],
            'draggable' => true
        ],
        [
            'position' => ['lat' => 28.625293, 'lng' => 79.817926],
            'label' => ['color' => 'white', 'text' => 'P2'],
            'draggable' => false
        ],
        [
            'position' => ['lat' => 28.625182, 'lng' => 79.81464],
            'label' => ['color' => 'white', 'text' => 'P3'],
            'draggable' => true
        ]
    ];

    return view('admin.orders.show', [
        'order' => $order,
        'offers' => $offers,
        'statuses' => $statuses,
        'initialMarkers' => $initialMarkers
    ]);
}

    public function create(){

        $users=User::select('id','name','type')->where('user_type','service_seeker')->get();
        $cars = Car::select('id', 'name_en', 'name_ar')->get();
        $shipments=ShipmentType::select('id','name_en','name_ar')->get();
        $payTypes=PaymentMethod::select('id','name')->where('type','payment')->get();

        return view('admin.orders.create', ['cars'=>$cars, 'shipments'=> $shipments,'users'=>$users, 'payTypes'=>$payTypes]);
    }
    public function store( Request $request)
    {


        $request->validate([
            'pick_up_address'      =>      'required',
            'pick_up_late'      =>      'required',
            'pick_up_long'      =>      'required',
            'drop_of_address'   =>      'required',
            'drop_of_late'      =>      'required',
            'drop_of_long'      =>      'required',
            'car_id'                =>      'required|exists:cars,id',
            'shipment_type_id'      =>      'required|exists:shipment_types,id',
            'shipment_details'      =>      'nullable|string',
            'payment_method_id'       =>      'required|exists:payment_methods,id',
            'shipping_date'         =>      'required',
            'size'                  =>      'required',
            'weight_ton'            =>      'required',
            'ton_price'             =>      'required',
            'notes'                 =>      'nullable|string',
            'desc'                  =>      'nullable|string'
        ]);
        if (!$request->has('breakable')) {
            $request->request->add(['breakable' => 0]);
        } else {
            $request->request->add(['breakable' => 1]);
        }
        if (!$request->has('spoil_quickly')) {
            $request->request->add(['spoil_quickly' => 0]);
        } else {
            $request->request->add(['spoil_quickly' => 1]);
        }
        DB::beginTransaction();
        $order=Order::create([
            'user_id'               =>  $request->user_id,
            'pick_up_address'       =>  $request->pick_up_address,
            'pick_up_late'          =>  $request->pick_up_late,
            'pick_up_long'          =>  $request->pick_up_long,
            'drop_of_address'          =>  $request->drop_of_address,
            'drop_of_late'          =>  $request->drop_of_late,
            'drop_of_long'          =>  $request->drop_of_long,
            'shipment_type_id'      =>  $request->shipment_type_id,
            'shipment_details'      =>  $request->shipment_details,
            'payment_method_id'       =>  $request->payment_method_id,
            'car_id'                =>  $request->car_id,
            'shipping_date'         =>  $request->shipping_date,
            'size'                  =>  $request->size,
            'weight_ton'            =>  $request->weight_ton,
            'ton_price'             =>  $request->ton_price,
            'total_price' =>($request->ton_price * $request->weight_ton),
            'breakable'             =>  $request->breakable,
            'spoil_quickly'         =>   $request->spoil_quickly,
            'desc'                  =>  $request->desc,
            'notes'                 =>  $request->notes
        ]);
        $data = [
            'title' => 'add',
            'body' => 'add_body',
            'target' => 'order',
            'link'  => route('admin.orders.index', ['id' => $order->id]),
            'target_id' => $order->id,
            'sender' => auth()->user()->name,
        ];
        $message = Lang::get('site.not_new_order_msg') . ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' ' . auth()->user()->name;
        $title = Lang::get('site.not_new_order');
        Notification::send($order->user, new FcmPushNotification($title, $message, [$order->user->fcm_token]));
        Notification::send($order->user, new LocalNotification($data));
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->orWhere('type', 'emp')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }
        DB::commit();

        session()->flash('success', __('site.added_success'));
        return redirect()->route('admin.orders.index');
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $order = Order::with(['user','shipmentType','car','offers'])->find($id);
        if (!$order) {
            session()->flash('errors', __('site.order_not_found'));
            return redirect()->route('admin.orders.index');
        }
        $users=User::select('id','name','type')->where('type','user')->orWhere('type','factory')->get();
        $cars = Car::select('id', 'name_en', 'name_ar')->get();
        $shipments=ShipmentType::select('id','name_en','name_ar')->get();
        $payTypes=PaymentMethod::select('id','name')->where('type','payment')->get();
        return view('admin.orders.edit', ['order' => $order,'cars'=>$cars, 'shipments'=> $shipments,'users'=>$users, 'payTypes'=>$payTypes]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $order = Order::find($id);
        if (!$order) {
            session()->flash('errors', __('site.order_not_found'));
            return redirect()->route('admin.order.index');
        }
        $request->validate([
            'pick_up_address'   =>      'required',
            'pick_up_late'      =>      'required',
            'pick_up_long'      =>      'required',
            'drop_of_address'   =>      'required',
            'drop_of_late'      =>      'required',
            'drop_of_long'      =>      'required',
            'car_id'                =>      'required|exists:cars,id',
            'shipment_type_id'      =>      'required|exists:shipment_types,id',
            'shipment_details'      =>      'nullable|string',
            'payment_method_id'       =>      'required|exists:payment_methods,id',
            'shipping_date'         =>      'required',
            'size'                  =>      'required',
            'weight_ton'            =>      'required',
            'ton_price'             =>      'required',
            'notes'                 =>      'nullable|string',
            'desc'                  =>      'nullable|string'
        ]);
        if (!$request->has('breakable')) {
            $request->request->add(['breakable' => 0]);
        } else {
            $request->request->add(['breakable' => 1]);
        }
        if (!$request->has('spoil_quickly')) {
            $request->request->add(['spoil_quickly' => 0]);
        } else {
            $request->request->add(['spoil_quickly' => 1]);
        }
        DB::beginTransaction();
        $order->update([
            'pick_up_address'   =>  $request->pick_up_address,
            'pick_up_late'      =>  $request->pick_up_late,
            'pick_up_long'      =>  $request->pick_up_long,
            'drop_of_address'   =>  $request->drop_of_address,
            'drop_of_late'      =>  $request->drop_of_late,
            'drop_of_long'      =>  $request->drop_of_long,
            'shipment_type_id'      =>  $request->shipment_type_id,
            'shipment_details'      =>  $request->shipment_details,
            'payment_method_id'       =>  $request->payment_method_id,
            'car_id'                =>  $request->car_id,
            'shipping_date'         =>  $request->shipping_date,
            'size'                  =>  $request->size,
            'weight_ton'            =>  $request->weight_ton,
            'ton_price'             =>  $request->ton_price,
            'total_price' =>($request->ton_price * $request->weight_ton),
            'breakable'             =>  $request->breakable,
            'spoil_quickly'         =>   $request->spoil_quickly,
            'desc'                  =>  $request->desc,
            'notes'                 =>  $request->notes
        ]);
        $data = [
            'title' => 'edit',
            'body' => 'edit_body',
            'target' => 'order',
            'link'  => route('admin.orders.index', ['id' => $order->id]),
            'target_id' => $order->id,
            'sender' => auth()->user()->name,
        ];
        $message = Lang::get('site.not_edit_order_msg') . ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' ' . auth()->user()->name;
        $title = Lang::get('site.not_edit_order');
        Notification::send($order->user, new FcmPushNotification($title, $message, [$order->user->fcm_token]));
        Notification::send($order->user, new LocalNotification($data));
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->orWhere('type', 'emp')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
            // if (!empty($user->fcm_token) && $user->id != auth()->user()->id) {
            //     Notification::send($user, new FcmPushNotification($title,$message, [$user->fcm_token]));
            // }
        }
        DB::commit();
        session()->flash('success', __('site.edited_success'));
        return redirect()->route('admin.orders.index');
    }

    public function changeStatus(Request $request,$id)
    {
        $request->validate(['status'=>'required']);
        $order = Order::find($id);
        if (!$order) {
            session()->flash('errors', __('site.order_not_found'));
            return redirect()->route('admin.orders.index');
        }
        DB::beginTransaction();
        $order->update(['status' => $request->status,'service_provider' => $request->service_provider]);
        $orderStatus=OrderStatus::create([
            'order_id'   =>  $order->id,
            'status'     =>  $order->status,
            'user_id'    =>  auth()->user()->id,
            'change_by'  =>   auth()->user()->name
        ]);
        $offers=Offer::where('order_id',$order->id)->get();
        foreach($offers as $offer){
            $offer->update(['status'=>$request->status]);
            $offerStatus=OfferStatus::create([
                'offer_id'   =>  $offer->id,
                'status'     =>  $order->status,
                'user_id'    =>  auth()->user()->id,
                'change_by'  =>   auth()->user()->name
            ]);
        }
        $data = [
            'title' => 'change_status',
            'body' => 'enable_body',
            'target' => 'order',
            'link'  => route('admin.orders.index', ['number' => $order->id]),
            'target_id' => $order->id,
            'sender' => auth()->user()->name,
        ];
        $users = User::where('user_type', 'manage')->orWhere('user_type','service_provider')->get();
        $message = Lang::get('site.not_approve_order_msg') . ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user') . ' '. auth()->user()->name;
        $title = Lang::get('site.not_approve_order');
        if($order->status=='approve'){
            $message = Lang::get('site.not_approve_order_msg') . ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user') . ' ' . auth()->user()->name;
            $title = Lang::get('site.not_approve_order');
        }
        elseif($order->status=='pick_up'){
            $message = Lang::get('site.not_pick_up_order_msg') . ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' '. auth()->user()->name;
            $title = Lang::get('site.not_pick_up_order');
        }
        elseif($order->status=='delivered'){
            $message = Lang::get('site.not_delivered_order_msg') . ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' '. auth()->user()->name;
            $title = Lang::get('site.not_delivered_order');
        }
        elseif($order->status=='complete'){
            $message = Lang::get('site.not_complete_order_msg') . ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' '. auth()->user()->name;
            $title = Lang::get('site.not_complete_order');
        }
        elseif ($order->status == 'cancel') {
            $message = Lang::get('site.not_cancel_order_msg') . ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' ' . auth()->user()->name;
            $title = Lang::get('site.not_cancel_order');
        } elseif ($order->status == 'pending') {
            $message = Lang::get('site.not_pend_order_msg') . ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' ' . auth()->user()->name;
            $title = Lang::get('site.not_pend_order');
        }
        Notification::send($order->user, new LocalNotification($data));
        Notification::send($order->user, new FcmPushNotification($title, $message, [$order->user->fcm_token]));
        foreach ($users as $user1) {
            // if (!empty($user1->fcm_token) && $user1->id != auth()->user()->id) {
            //     Notification::send($user1, new FcmPushNotification($title, $message, [$user1->fcm_token]));
            // }
            Notification::send($user1, new LocalNotification($data));
        }
        DB::commit();
        session()->flash('success', __('site.enable_success'));
        return redirect()->route('admin.orders.index');
    }

    public function completeOrder(Request $request, $id)
    {
        $request->validate(['status' => 'required']);
        $order = Order::find($id);
        if (!$order) {
            session()->flash('errors', __('site.order_not_found'));
            return redirect()->route('admin.orders.index');
        }
        DB::beginTransaction();
        $offer = Offer::where('order_id',$order->id)->where('user_id',$order->service_provider)->where('status','approve')->get()->first();
        $order->update(['status' => $request->status]);
        $orderStatus = OrderStatus::create([
            'order_id'   =>  $order->id,
            'status'     =>  $order->status,
            'user_id'    =>  auth()->user()->id,
            'change_by'  =>   auth()->user()->name
        ]);
        $offer->update(['status' => $request->status]);
        $offerStatus = OfferStatus::create([
            'offer_id'   =>  $offer->id,
            'status'     =>  $request->status,
            'user_id'    =>  auth()->user()->id,
            'change_by'  =>   auth()->user()->name
        ]);
        $data = [
            'title' => 'change_status',
            'body' => 'enable_body',
            'target' => 'order',
            'link'  => route('admin.orders.index', ['number' => $order->id]),
            'target_id' => $order->id,
            'sender' => auth()->user()->name,
        ];
        $message = Lang::get('site.not_complete_order') . ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user') . ' ' . auth()->user()->name;
        $title = Lang::get('site.not_complete_order_msg');
        Notification::send($order->user, new LocalNotification($data));

        if (!empty($order->user->fcm_token) && $order->user->id != auth()->user()->id) {
            Notification::send($order->user, new FcmPushNotification($title, $message, [$order->user->fcm_token]));
        }

        Notification::send($offer->user, new LocalNotification($data));
        if (!empty($offer->user->fcm_token) && $offer->user->id != auth()->user()->id) {
            Notification::send($offer->user, new FcmPushNotification($title, $message, [$offer->user->fcm_token]));
        }

        Notification::send($offer->driver, new LocalNotification($data));
        if (!empty($offer->driver->fcm_token) && $offer->driver->id != auth()->user()->id) {
            Notification::send($offer->driver, new FcmPushNotification($title, $message, [$offer->driver->fcm_token]));
        }
        $users = User::where('user_type', 'manage')->get();
        foreach ($users as $user1) {
            // if (!empty($user1->fcm_token) && $user1->id != auth()->user()->id) {
            //     Notification::send($user1, new FcmPushNotification($title, $message, [$user1->fcm_token]));
            // }
            Notification::send($user1, new LocalNotification($data));
        }
        DB::commit();
        session()->flash('success', __('site.enable_success'));
        return redirect()->back();
    }
    public function changeOfferStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required']);
        $offer = Offer::find($id);
        if (!$offer) {
            session()->flash('errors', __('site.offer_not_found'));
            return redirect()->back();
        }
        DB::beginTransaction();
        $order=Order::find($offer->order_id);
        $offer->update(['status' => $request->status]);
        $offerStatus = OfferStatus::create([
            'offer_id'   =>  $offer->id,
            'status'     =>  $request->status,
            'user_id'    =>  auth()->user()->id,
            'change_by'  =>   auth()->user()->name
        ]);
        $data = [
            'title' => 'change_status',
            'body' => 'enable_body',
            'target' => 'order',
            'link'  => route('admin.orders.index', ['number' => $order->id]),
            'target_id' => $order->id,
            'sender' => auth()->user()->name,
        ];
        $message = Lang::get('site.not_approve_offer_msg') . ' ' . $offer->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' '. auth()->user()->name;
        $title = Lang::get('site.not_approve_offer');
        if ($offer->status == 'approve') {
            $message = Lang::get('site.not_approve_offer_msg') . ' ' . $offer->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' '. auth()->user()->name;
            $title = Lang::get('site.not_approve_offer');
        } elseif ($offer->status == 'wait_accept') {
            $message = Lang::get('site.not_wait_accept_offer_msg') . ' ' . $offer->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' '. auth()->user()->name;
            $title = Lang::get('site.not_wait_accept_offer');
        } elseif ($offer->status == 'cancel') {
            $message = Lang::get('site.not_cancel_offer_msg') . ' ' . $offer->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' '. auth()->user()->name;
            $title = Lang::get('site.not_cancel_offer');
        } elseif ($offer->status == 'pending') {
            $message = Lang::get('site.not_pend_offer_msg') . ' ' . $offer->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' ' . auth()->user()->name;
            $title = Lang::get('site.not_pend_offer');
        }
        Notification::send($order->user, new LocalNotification($data));
        Notification::send($order->user, new FcmPushNotification($title, $message, [$order->user->fcm_token]));
        Notification::send($offer->user, new LocalNotification($data));
        Notification::send($offer->user, new FcmPushNotification($title, $message, [$offer->user->fcm_token]));
        if($offer->driver_id != $offer->user_id){
            Notification::send($offer->driver, new LocalNotification($data));
            Notification::send($offer->driver, new FcmPushNotification($title, $message, [$offer->driver->fcm_token]));
        }
        $users = User::where('user_type', 'manage')->get();
        foreach ($users as $user1) {
            Notification::send($user1, new LocalNotification($data));
            //Notification::send($user1, new FcmPushNotification($title, $message, [$offer->driver->fcm_token]));
        }
        DB::commit();
        session()->flash('success', __('site.enable_success'));
        return redirect()->back();
    }
    public function approveOffer(Request $request ,$id){
        $request->validate(['status' => 'required']);
        $offer = Offer::find($id);
        if (!$offer) {
            session()->flash('errors', __('site.offer_not_found'));
            return redirect()->back();
        }
        DB::beginTransaction();
        $order = Order::find($offer->order_id);
        $order->update(['status' => $request->status,'service_provider'=>$offer->user_id]);
        $orderStatus = OrderStatus::create([
            'order_id'   =>  $order->id,
            'status'     =>  $order->status,
            'user_id'    =>  auth()->user()->id,
            'change_by'  =>   auth()->user()->name
        ]);
        $room = ChatRoom::create([
            'title' => 'Order # ' . $order->id,
            'order_id' => $order->id,
        ]);
        $service_provider_commission = 0;
        if ($offer->user->type == 'driver') {
            $service_provider_commission = Setting('driver_commission');
        } elseif ($offer->user->type == 'driversCompany') {
            $service_provider_commission = Setting('company_commission');
        }
        $service_seeker_fee = 0;
        if ($offer->user->type == 'user') {
            $service_seeker_fee = Setting('customer_fee');
        } elseif ($offer->user->type == 'factory') {
            $service_seeker_fee = Setting('company_fee');
        }

        $orderaccountant = OrderAccountant::updateOrCreate([
            'order_id' => $order->id,
            'service_provider_amount' => $offer->price,
            'service_provider_commission' => $service_provider_commission,
            'service_seeker_fee' => $service_seeker_fee,
            'vat'=>Setting('vat'),
            'fine' => Setting('fine'),
            'operating_costs' => Setting('operating_costs'),
            'diesel_cost' => Setting('diesel_cost_per_km'),
            'expenses' => Setting('expenses'),
        ]);

        $room = ChatRoom::updateOrCreate([
            'title' => 'Order # ' . $order->id,
            'order_id' => $order->id,
            'description' => 'Chat Room For Order Number : ' . $order->id
        ]);
        $room->join($order->user_id);
        if ($offer->user->type == 'driver') {
            $room->join($offer->user_id);
        } else {
            $room->join($offer->user_id);
            $room->join($offer->driver);
        }
        $offer->update(['status' => $request->status]);
        $offerStatus = OfferStatus::create([
            'offer_id'   =>  $offer->id,
            'status'     =>  $request->status,
            'user_id'    =>  auth()->user()->id,
            'change_by'  =>   auth()->user()->name
        ]);
        $offers=Offer::where('order_id',$order->id)->get();
        foreach($offers as $offr) {
            if($offer->id != $offr->id){
                $offr->update(['status' =>'cancel']);
                $offerStatus = OfferStatus::create([
                    'offer_id'   =>  $offr->id,
                    'status'     =>  'cancel',
                    'user_id'    =>  auth()->user()->id,
                    'change_by'  =>   auth()->user()->name
                ]);
            }
        }
        $data = [
            'title' => 'change_status',
            'body' => 'enable_body',
            'target' => 'order',
            'link'  => route('admin.orders.index', ['number' => $order->id]),
            'target_id' => $order->id,
            'sender' => auth()->user()->name,
        ];
        $message = Lang::get('site.not_approve_offer_msg') . ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user') . ' ' . auth()->user()->name;
        $title = Lang::get('site.not_approve_offer');
        Notification::send($order->user, new LocalNotification($data));
        Notification::send($order->user, new FcmPushNotification($title, $message, [$order->user->fcm_token]));
        Notification::send($offer->user, new LocalNotification($data));
        Notification::send($offer->user, new FcmPushNotification($title, $message, [$offer->user->fcm_token]));
        if($offer->user_id != $offer->driver_id){
        Notification::send($offer->driver, new LocalNotification($data));
            Notification::send($offer->driver, new FcmPushNotification($title, $message, [$offer->driver->fcm_token]));
        }
        $users = User::where('user_type', 'manage')->get();
        foreach ($users as $user1) {
            Notification::send($user1, new LocalNotification($data));
           // Notification::send($user1, new FcmPushNotification($title, $message, [$user1->fcm_token]));
        }
        DB::commit();
        session()->flash('success', __('site.enable_success'));
        return redirect()->back();
    }
    public function export()
    {
        return Excel::download(new OrderExport,  Lang::get('site.orders') . '-' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx');
    }
}
