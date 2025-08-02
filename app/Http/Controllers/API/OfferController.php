<?php

namespace App\Http\Controllers\API;

use App\Models\ChatRoom;
use App\Models\Offer;
use App\Models\OfferStatus;
use App\Models\Order;
use App\Models\OrderAccountant;
use App\Models\OrderStatus;
use App\Models\User;
use App\Models\Car;
use App\Models\UserData;
use  App\Models\Evaluate;
use App\Notifications\FcmPushNotification;
use App\Notifications\LocalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class OfferController extends BaseController
{

    public function __construct()
    {
        $this->middleware('api.acceptjson');
    }
public function getByAgencyId($id)
{
    // Fetch the service provider with evaluations and user data
    $service_provider = User::with(['userData:id,user_id,image,latitude,longitude,location'])
        ->select('id', 'name')
        ->find($id);

    if (!$service_provider) {
        return $this->sendError('Service provider not found.');
    }

    $service_provider->evaluate_rate = Evaluate::where('user2_id', $service_provider->id)->avg('rate') ?? 0;
    $service_provider->evaluate_count = Evaluate::where('user2_id', $service_provider->id)->count();

    // Fetch offers with related order and preload driver details
    $offers = Offer::with([
        'order:id,user_id,car_id,pick_up_address,drop_of_address,shipment_type_id,status',
        'driver.userData:id,user_id,image,latitude,longitude,location,track_type',
        'driver.userData.car:id,name_ar,name_en,image,weight'
    ])
        ->where('user_id', $id)
        ->select('id', 'vat', 'ton_price', 'price', 'status', 'sub_total', 'order_id', 'user_id', 'driver_id', 'drivers_ids')
        ->latest()
        ->get();

    // Pick the first offer's driver (if available)
    $firstDriver = $offers->first()?->driver;

    if ($firstDriver) {
        $firstDriver->evaluate_rate = Evaluate::where('user2_id', $firstDriver->id)->avg('rate') ?? 0;
        $firstDriver->evaluate_count = Evaluate::where('user2_id', $firstDriver->id)->count();
    }

    $offersFormatted = $offers->map(function ($offer) {
        return [
            'id' => $offer->id,
            'vat' => $offer->vat,
            'ton_price' => $offer->ton_price,
            'price' => $offer->price,
            'status' => $offer->status,
            'sub_total' => $offer->sub_total,
            'order_id' => $offer->order_id,
            'user_id' => $offer->user_id,
            'driver_id' => $offer->driver_id,
            'drivers_ids' => $offer->drivers_ids,
            'order' => $offer->order
        ];
    });

    return $this->sendResponse([
        'service_provider' => $service_provider,
        'driver' => $firstDriver,
        'count' => $offers->count(),
        'offers' => $offersFormatted
    ], 'Offers information with order details.');
}

 public function getByUserId($id)
{
    // Fetch offers and include order details
    $offers = Offer::select('id', 'vat', 'ton_price', 'price', 'status', 'sub_total', 'order_id', 'user_id', 'driver_id', 'drivers_ids')
        ->with(['order' => function ($query) {
            $query->select('id', 'user_id', 'car_id', 'pick_up_address', 'drop_of_address', 'shipment_type_id', 'status');
        }])
        ->where('user_id', $id)
        ->latest()
        ->get();

    // Fetch service provider (user who created the offer)
    $service_provider = User::select('id', 'name')
        ->with(['userData' => function ($query) {
            $query->select('image', 'latitude', 'longitude', 'location', 'user_id');
        }])
        ->where('id', $id)
        ->first();

    if ($service_provider) {
        $service_provider['evaluate_rate'] = Evaluate::where('user2_id', $service_provider->id)->avg('rate');
        $service_provider['evaluate_count'] = Evaluate::where('user2_id', $service_provider->id)->count();
    }

    $avg1 = 0;
    $driver = null;
    $evaluate_count = 0;

    if (!empty($offers[0])) {
        // Fetch driver details
        $driver = User::select('id', 'name')
            ->with(['userData' => function ($query) {
                $query->select('image', 'latitude', 'longitude', 'location', 'user_id', 'track_type')
                    ->with('car', function ($carQuery) {
                        $carQuery->select('id', 'name_ar', 'name_en', 'image', 'weight');
                    });
            }])
            ->where('id', $offers[0]->driver_id)
            ->first();

        if ($driver) {
            $avg1 = Evaluate::where('user2_id', $driver->id)->avg('rate');
            $evaluate_count = Evaluate::where('user2_id', $driver->id)->count();
        }
    }

    // Add driver evaluations
    if ($driver) {
        $driver['evaluate_rate'] = $avg1;
        $driver['evaluate_count'] = $evaluate_count;
    }

    // Include order details in each offer
    $offersWithOrders = $offers->map(function ($offer) {
        return [
            'id' => $offer->id,
            'vat' => $offer->vat,
            'ton_price' => $offer->ton_price,
            'price' => $offer->price,
            'status' => $offer->status,
            'sub_total' => $offer->sub_total,
            'order_id' => $offer->order_id,
            'user_id' => $offer->user_id,
            'driver_id' => $offer->driver_id,
            'drivers_ids' => $offer->drivers_ids,
            'order' => $offer->order // Include order data here
        ];
    });

    // Prepare response data
    $success['service_provider'] = $service_provider;
    $success['driver'] = $driver;
    $success['count'] = $offers->count();
    $success['offers'] = $offersWithOrders;

    return $this->sendResponse($success, 'Offers information with order details.');
}

    public function getByDriverId(Request $request,$id)
    {

        /*$offers = Offer::select('id','price','order_id','user_id','driver_id','status','created_at')->
       where('driver_id', $id)->latest()->get();
        $service_provider = User::select('id','name')->with('userData',function($r){
                $r->select('image','latitude','longitude','location','user_id')->get();
            })->where('id',$offers[0]->user_id)->first();
        $avg= Evaluate::where('user2_id',$service_provider->id)->avg('rate');
        $service_provider['evaluate_rate']=$avg;
        $service_provider['evaluate_count']=Evaluate::where('user2_id',$service_provider->id)->count();
        $driver   =  User::select('id','name')->with('userData',function($r){
                $r->select('image','latitude','longitude','location','user_id','track_type')->
                with('car',function($c){
            $c->select('id','name_ar','name_en','image','weight');
        })->get();
            })->where('id',$id)->first();
        $avg1= Evaluate::where('user2_id',$id)->avg('rate');
      //  array_push($driver,$avg1);
        $driver['evaluate_rate']=$avg1;
        $driver['evaluate_count']=Evaluate::where('user2_id',$offers[0]->driver_id)->count();
        $success['service_provider'] =  $service_provider;
        $success['driver'] =  $driver;
        */
        $offers = Offer::select('id','vat','ton_price','price','sub_total','price','order_id','user_id','driver_id','status','created_at')->when($request->status, function ($query) use ($request) {
            return $query->where('status',  $request->status);
        })->
        with('order',function($q){
            $q->select('id','pick_up_address','pick_up_late','pick_up_long','drop_of_address', 'drop_of_late','drop_of_long','status')->get();
        })->
       where('driver_id', $id)->latest()->get();
        $success['count'] =  $offers->count();
        $success['offers'] =  $offers;
        return $this->sendResponse($success, 'Offers information.');
    }
    public function getByStatus(Request $request)
    {
        $offers = Offer::select('id','vat','ton_price','price','sub_total','price','order_id','user_id','driver_id','status','created_at')
        ->when($request->status, function ($query) use ($request) {
            return $query->where('status',  $request->status);
        })->when($request->user_id, function ($query) use ($request) {
            return $query->where('user_id',  $request->user_id);
        })->when($request->order_id, function ($query) use ($request) {
            return $query->where('order_id',  $request->order_id);
        })->when($request->driver_id, function ($query) use ($request) {
            return $query->where('driver_id',  $request->driver_id);
        })->when($request->price, function ($query) use ($request) {
            return $query->where('price',  $request->price);
        })->when($request->status, function ($query) use ($request) {
            return $query->where('status',  $request->status);
        })->
        with('order',function($q){
            $q->select('id','pick_up_address','pick_up_late','pick_up_long','drop_of_address', 'drop_of_late','drop_of_long','status')->get();
        })->latest()->get();
        $success['count'] =  $offers->count();
        $success['offers'] =  $offers;
        return $this->sendResponse($success, 'offers information.');
    }

    public function getOrderDetails($order) {
                $order = Order::with(['car','shipmentType','statuses','evaluate','paymentType','transaction','accountant'])
        ->find($order->id);


        if (!$order) {
			$msgs=['Order not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
        $user=User::find($order->user_id);
        $userData=UserData::where('user_id',$user->id)->get()->first();

        $user['image']=$userData->image;
        $avg= Evaluate::with(['user'])->where('user_id',$user->id)->avg('rate');
        $eval_count= Evaluate::with(['user'])->where('user_id',$user->id)->count();
        $user['rate_avg']= number_format($avg,2);
        $user['evaluates_count']=$eval_count;
        //$order['service_provider']=null;
        if($order->service_provider){
        $service_provider=User::find($order->service_provider);
        $service_providerData=UserData::where('user_id',$service_provider->id)->get()->first();
        $service_provider['image']=$service_providerData->image;
        $service_provider['driver_id']=$service_provider->id;
        $service_provider['driver_image']=$service_providerData->image;
        $service_provider['driver_name']=$service_provider->name;
        $avg1= Evaluate::with(['user'])->where('user_id',$service_provider->id)->avg('rate');
        $service_provider['rate_avg']= number_format($avg1,2);
        $eval_count1= Evaluate::with(['user'])->where('user_id',$service_provider->id)->count();
        $service_provider['evaluates_count']=$eval_count1;
        if( auth()->user()->type=='driverCompany'){
            $userAssigned=OrdersInvites::where('order_id',$order->id)->get();
            $DriversAssigned=[];
            foreach($userAssigned as $userass){
                $driv=User::select('id','name','phone','type','active')->find($userass->driver_id);
                $drivData=UserData::where('user_id',$userass->driver_id)->get()->first();
                $driv['image']=$userData->image;
                $driver_car=Car::find($drivData->track_type);
                if($driver_car){
                $driv['car_id']=$driver_car->id;
                $driv['car_name_ar']=$driver_car->name_ar;
                $driv['car_name_en']=$driver_car->name_en;
                $driv['car_weight']=$driver_car->weight;
                $driv['car_image']=$driver_car->car_image;
                }else{
                    $driv['car_id']=null;
                    $driv['car_name_ar']=null;
                    $driv['car_name_en']=null;
                    $driv['car_weight']=null;
                    $driv['car_image']=null;
                }
                $avg= Evaluate::with(['user'])->where('user_id',$userass->driver_id)->avg('rate');
                $eval_count= Evaluate::with(['user'])->where('user_id',$userass->driver_id)->count();
                $driver_offer=Offer::where('driver_id',$userass->driver_id)->get()->first();
                if($driver_offer){
                    $driv['vat']=$driver_offer->vat;
                    $driv['ton_price']=number_format($driver_offer->ton_price,2);
                    $driv['price']=number_format($driver_offer->price,2);
                    $driv['sub_total']=number_format($driver_offer->sub_total,2);
                }else{
                    $driv['vat']=0;
                    $driv['ton_price']=0.00;
                    $driv['price']=0.00;
                    $driv['sub_total']=0.00;
                }
                $driv['rate_avg']= number_format($avg,2) ?? 0;

                $driv['evaluates_count']=$eval_count;
                array_push($DriversAssigned,$driv);
            }
            if($userAssigned ){
                $order['DriversAssigned']=$DriversAssigned;
            }
        }
            $order['user']=$user;
            $order['service_provider']=$service_provider;
        }
        $offer=Offer::where('order_id',$order->id)->where('driver_id',$order->service_provider)->where('status','!=','pending')->get()->first();
        if($offer){
            $success['vat']=$offer->vat;
            $success['shipping_price']=$offer->price;
            $success['subtotal']=$offer->sub_total;
        }else{
            $success['vat']=0;
            $success['shipping_price']=0;
            $success['subtotal']=0;
        }

        $success['order'] =  $order;
        return $success;
    }
 public function getByOrderId($id)
{
    $offers = Offer::select('id','vat','ton_price','price','sub_total','order_id','user_id','driver_id','created_at')
        ->where('order_id', $id)
        ->with(['user' => function($q) {
            $q->select('id','name','type','phone','active')->withCount('evaluates');
        }, 'driver' => function($q) {
            $q->select('id','name','type','phone','active')->withCount('evaluates');
        }])
        ->get();

    $offers1 = [];
    $count = 0;
    foreach($offers as $offer){
        $userData = UserData::select('image','latitude','longitude','location','user_id')
            ->where('user_id', $offer->user_id)->first();
        $avg = Evaluate::where('user2_id', $offer->user_id)->avg('rate');
        $offer->user['rate_avg'] = $avg ?? 0;
        $offer->user['image'] = $userData->image;
        $offer->user['latitude'] = $userData->latitude;
        $offer->user['longitude'] = $userData->longitude;
        $offer->user['location'] = $userData->location;

        $driverData = UserData::select('image','latitude','longitude','location','user_id','track_type')
            ->where('user_id', $offer->driver_id)->first();
        $offer->driver['image'] = $driverData->image;
        $offer->driver['latitude'] = $driverData->latitude;
        $offer->driver['longitude'] = $driverData->longitude;
        $offer->driver['location'] = $driverData->location;

        $driverCar = Car::select('id','name_ar','name_en','image','frames','weight')->find($driverData->track_type);
        if ($driverCar) {
            $offer->driver['car_id'] = $driverCar->id;
            $offer->driver['car_name_ar'] = $driverCar->name_ar;
            $offer->driver['car_name_en'] = $driverCar->name_en;
            $offer->driver['car_weight'] = $driverCar->weight;
            $offer->driver['car_frames'] = $driverCar->frames;
            $offer->driver['car_image'] = $driverCar->image;
        }

        array_push($offers1, $offer);
        $count++;
    }

    $success['count'] = $count;
    $success['offers'] = $offers1;

    // Fix: Ensure the variable $offer exists before use
    if (!empty($offers1)) {
        $firstOffer = $offers1[0];
        $details = $this->getOrderDetails($firstOffer->order);
        foreach ($details as $key => $item) {
            $success[$key] = $item;
        }
    }

    return $this->sendResponse($success, 'offers information.');
}

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //'user_id' => 'required|exists:users,id',
            'order_id' => 'required|exists:orders,id',
            'driver_id' => 'required|exists:users,id',
            //'drivers_ids'=>'nullable|array',
            'ton_price' => 'required',
            'status' => 'required',
            'desc' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $msgs = [];
            foreach ($errors->all() as  $ind => $message) {
                array_push($msgs, $message);
            }
            return $this->sendError('Validation Error.', $msgs);
        }
        $offer1 = Offer::where('driver_id', $request->driver_id)->where('order_id', $request->order_id)->get()->first();
        if ($offer1) {
            $sccess['offer'] = $offer1;
			$msgs=['Offer not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
        $drivers_ids=[];
        $drivers=[];
        foreach((array)$request->drivers_ids as $dri_id){
            $driver=User::select('id','name','type','phone','active')->find($dri_id);
            $driverid=$driver->id;
            array_push($drivers,$driver);
            array_push($drivers_ids,$driverid);
        }
        DB::beginTransaction();
        $driver =User::find($request->driver_id);
        $user_id=$driver->id;
        $commission  =Setting('driver_commission');
        if($driver->userData->company_id!=""){
            $commission  =Setting('company_commission');
            $user_id=$driver->userData->company_id;
        }
        $company =User::find($driver->userData->company_id);
        $order=Order::find($request->order_id);
        if($driver->userData->company_id==null){
            $commission  =Setting('driver_commission');
        }
        $price =$request->ton_price  *  $order->weight_ton+((( $request->ton_price  *  $order->weight_ton ) * $commission) /100 ) +Setting('operating_costs')+ Setting('expenses');
        $offer = Offer::create([
            'user_id' => $user_id,
            'order_id' => $request->order_id,
            'driver_id' => $request->driver_id,
            'drivers_ids'=>json_encode($drivers_ids,true),
            'vat'=>Setting('vat'),
            'ton_price'=>$request->ton_price,
            'sub_total'=>$price+(($price * Setting('vat') ) / 100),
            'price' => $price,
            'status' => $request->status,
            'desc' => $request->desc,
            'notes' => $request->notes,
        ]);
        $offer['drivers']=$drivers;
        $user = User::find($offer->user_id);
        $data = [
            'title' => 'add_offer',
            'body' => 'add_body',
            'target' => 'order',
            'link'  => route('admin.orders.index', ['number' => $offer->order_id]),
            'target_id' => $offer->order_id,
            'sender' => $user->name,
        ];
        $message = Lang::get('site.not_new_offer_msg') . ' ' . $offer->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' ' . auth()->user()->name;
        $title = Lang::get('site.not_new_offer');
        $userFCM=User::find($order->user_id);
        Notification::send($userFCM, new FcmPushNotification($title, $message, [$userFCM->fcm_token]));
        Notification::send($userFCM, new LocalNotification($data));
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
            //   Notification::send($user, new FcmPushNotification($title, $message, [$user->fcm_token]));
        }

        DB::commit();
        $success['offer'] =  $offer;
        return $this->sendResponse($success, 'Offer created successfully.');
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
        //    'user_id' => 'required|exists:users,id',
            'order_id' => 'required|exists:orders,id',
            'driver_id' => 'required|exists:users,id',
          //  'drivers_ids'=>'required|array',
            'ton_price' => 'required',
            'status' => 'required',
            'desc' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $msgs = [];
            foreach ($errors->all() as  $ind => $message) {
                array_push($msgs, $message);
            }
            return $this->sendError('Validation Error.', $msgs);
        }
        $offer = Offer::where('driver_id', $request->driver_id)->find($id);
        if (!$offer) {
			$msgs=['Offer not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
        $drivers_ids=[];
        $drivers=[];
        foreach((array)$request->drivers_ids as $dri_id){
            $driver=User::select('id','name','type','phone','active')->find($dri_id);
            $driverid=$driver->id;
            array_push($drivers,$driver);
            array_push($drivers_ids,$driverid);
        }
        DB::beginTransaction();

        $driver =User::find($request->driver_id);
        $user_id=$driver->id;
        $commission  =Setting('driver_commission');
        if($driver->userData->company_id!=""){
            $commission  =Setting('company_commission');
            $user_id=$driver->userData->company_id;
        }
        $company =User::find($driver->userData->company_id);
        $order=Order::find($request->order_id);
        if($driver->userData->company_id==null){
            $commission  =Setting('driver_commission');
        }
        $price =$request->ton_price  *  $order->weight_ton+((( $request->ton_price  *  $order->weight_ton ) * $commission) /100 ) +Setting('operating_costs')+ Setting('expenses');

        $offer->update([
            'user_id'=>$user_id,
            'driver_id' => $request->driver_id,
           // 'drivers_ids'=>json_encode($drivers_ids,true),
            'vat'=>Setting('vat'),
            'ton_price'=>$request->ton_price,
            'sub_total'=>$price+(($price * Setting('vat') ) / 100),
            'price' => $price,
            'status' => $request->status,
            'desc' => $request->desc,
            'notes' => $request->notes,
        ]);
        $offer['drivers']=$drivers;
        $user = User::find($offer->user_id);
        $data = [
            'title' => 'edit_offer',
            'body' => 'edit_body',
            'target' => 'order',
            'link'  => route('admin.orders.index', ['number' => $offer->order_id]),
            'target_id' => $offer->order_id,
            'sender' => $user->name,
        ];
        $message = Lang::get('site.not_edit_offer_msg') . ' ' . $offer->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' ' . auth()->user()->name;
        $title = Lang::get('site.not_edit_offer');
        Notification::send($offer->order->user, new FcmPushNotification($title, $message, [$offer->order->user->fcm_token]));
        Notification::send($offer->order->user, new LocalNotification($data));
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
        foreach ($users as $user) {
            //  Notification::send($user, new FcmPushNotification($title, $message, [$user->fcm_token]));
            Notification::send($user, new LocalNotification($data));
        }
        DB::commit();
        $success['offer'] =  $offer;
        return $this->sendResponse($success, 'Offer created successfully.');
    }
    public function show($id)
    {
        $offer = Offer::find($id);
        $drivers=[];
        if(isset($offer->drivers_ids)){
              foreach(json_decode($offer->drivers_ids) as $dirver_id){
            $driv=User::select('id','name','type','phone','active')->get();
            array_push($drivers,$driv);
        }
        }
      
        $offer['drivers']=$drivers;
        $success['offer'] =  $offer;
        return $this->sendResponse($success, 'Offer information.');
    }
    public function changeStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'offer_id' => 'required|exists:offers,id',
            'status' => 'required',
            'change_by' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $msgs = [];
            foreach ($errors->all() as  $ind => $message) {
                array_push($msgs, $message);
            }
            return $this->sendError('Validation Error.', $msgs);
        }
        $offer = Offer::find($id);
        if (!$offer) {
			$msgs=['Offer not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
        DB::beginTransaction();

        $offer_status = OfferStatus::create([
            'user_id' => $request->user_id,
            'offer_id' => $request->offer_id,
            'status' => $request->status,
            'change_by' => $request->change_by,
        ]);
        $offer->update([
            'status' => $request->status,
        ]);
        if ($offer->status == 'approve') {
            $order = Order::find($offer->order_id);
            $order->update(['status' => 'approve', 'service_provider' => $offer->driver_id,'offer_id'=>$offer->id]);
            OrderStatus::create([
                'user_id' => $request->user_id,
                'order_id' => $offer->order_id,
                'status' => 'approve',
                'change_by' => $request->change_by,
            ]);
            $service_provider_commission=0;
            if($offer->user->type=='driver'){
                $service_provider_commission=Setting('driver_commission');
            }
            elseif ($offer->user->type == 'driversCompany') {
                $service_provider_commission = Setting('company_commission');
            }
            $service_seeker_fee=0;
            if ($offer->user->type == 'user') {
                $service_seeker_fee = Setting('customer_fee');
            } elseif ($offer->user->type == 'factory') {
                $service_seeker_fee = Setting('company_fee');
            }

            $orderaccountant = OrderAccountant::updateOrCreate([
                'order_id' => $order->id,
                'service_provider_amount' => $offer->price ,
                'service_provider_commission'=> $service_provider_commission,
                'service_seeker_fee'=> $service_seeker_fee,
                'vat'=>Setting('vat'),
                'fine'=>Setting('fine'),
                'operating_costs'=>Setting('operating_costs'),
                'diesel_cost'=>Setting('diesel_cost_per_km'),
                'expenses'=>Setting('expenses'),
            ]);
            if ($offer->status == 'approve' && $order->user->type=='user'  ) {
                $driver=UserData::where('user_id',$offer->driver_id)->get()->first();
                $driver->update(['status'=>'busy','balance'=>$driver->balance+($orderaccountant->service_provider_amount - ($orderaccountant->service_provider_amount * 5) /100)]);
                if($driver->company_id){
                    $comData=UserData::where('user_id',$driver->company_id)->get()->first();
                    $comData->update(['balance'=>$comData->balance + ($orderaccountant->service_provider_amount * 5 )/ 100]);
                }
                $user=UserData::where('user_id',$order->user_id)->get()->first();
                $user->update(['outstanding_balance'=>$offer->sub_total]);
            }
            if ($offer->status == 'approve' && $order->user->type=='factory'  ) {
                $driver=UserData::where('user_id',$offer->driver_id)->get()->first();
                $driver->update(['status'=>'busy','pending_balance'=>$driver->pending_balance+($orderaccountant->service_provider_amount - ($orderaccountant->service_provider_amount * 5) / 100)]);
                if($driver->company_id){
                    $comData=UserData::where('user_id',$driver->company_id)->get()->first();
                    $comData->update(['pending_balance'=>$comData->pending_balance + ($orderaccountant->service_provider_amount * 5 )/ 100]);
                }
            }
            if ($offer->status == 'approve' && $order->user->type=='factory'  ) {
                $customer=UserData::where('user_id',$order->user_id)->get()->first();
                $customer->update(['outstanding_balance'=>$customer->outstanding_balance + $offer->sub_total]);
            }
            $room=ChatRoom::updateOrCreate([
                'title'=>'Order # ' .$order->id,
                'order_id'=>$order->id,
                'description'=>'Chat Room For Order Number : '.$order->id
            ]);
            $room->join($order->user_id);
            $room->join($offer->user_id);
            $room->join($offer->driver_id);

            $offers = Offer::where('order_id', $offer->order_id)->where('id', '!=', $offer->id)->get();
            foreach ($offers as $offer1) {
                Log::info('offer1 : ' . $offer1);
                $offer1->update(['status' => 'cancel', 'notes' => 'canceled by ' . $request->change_by]);
                OfferStatus::create([
                    'user_id' => $request->user_id,
                    'offer_id' => $offer1->id,
                    'status' => 'cancel',
                    'change_by' => $request->change_by,
                ]);
                Log::info('offer : ' . $offer1);
            }
        }
        $offer = Offer::with('statuses')->find($id);
        $user = User::find($offer->user_id);
        $data = [
            'title' => $request->status,
            'body' => 'add_body',
            'target' => 'offer',
            'link'  => route('admin.orders.index', ['number' => $offer->order_id]),
            'target_id' => $offer->id,
            'sender' => $user->name,
        ];
        $message = Lang::get('site.not_approve_offer_msg') . ' ' . $offer->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' ' . auth()->user()->name;
        $title = Lang::get('site.not_approve_offer');
        if ($offer->status == 'approve') {
            $message = Lang::get('site.not_approve_offer_msg') . ' ' . $offer->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' ' . auth()->user()->name;
            $title = Lang::get('site.not_approve_offer');
        } elseif ($offer->status == 'wait_accept') {
            $message = Lang::get('site.not_wait_accept_offer_msg') . ' ' . $offer->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' ' . auth()->user()->name;
            $title = Lang::get('site.not_wait_accept_offer');
        } elseif ($offer->status == 'cancel') {
            $message = Lang::get('site.not_cancel_offer_msg') . ' ' . $offer->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' ' . auth()->user()->name;
            $title = Lang::get('site.not_cancel_offer');
        } elseif ($offer->status == 'open') {
            $message = Lang::get('site.not_open_offer_msg') . ' ' . $offer->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' ' . auth()->user()->name;
            $title = Lang::get('site.not_open_offer');
        }
        Notification::send($offer->order->user, new LocalNotification($data));
        if (!empty($offer->order->user->fcm_token)) {
            Notification::send($offer->order->user, new FcmPushNotification($title, $message, [$offer->order->user->fcm_token]));
        }
         Notification::send($offer->user, new LocalNotification($data));
        if (!empty($offer->user->fcm_token)) {
            Notification::send($offer->user, new FcmPushNotification($title, $message, [$offer->user->fcm_token]));
        }
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
            if (!empty($user->fcm_token)) {
                Notification::send($user, new FcmPushNotification($title, $message, [$user->fcm_token]));
            }
        }
        DB::commit();
        $success['offer'] =  $offer;
        
        /* -------- NEW: update driver availability -------- */
$driverData = UserData::where('user_id', $offer->driver_id)->first();
if ($driverData) {
    switch ($offer->status) {
        case 'approve':                       // offer accepted
            $driverData->update(['status' => 'busy']);
            break;

        case 'pick_up':                       // driver en-route / loading
        case 'complete':                      // still handling shipment
            $driverData->update(['status' => 'in_Shipment']);
            break;

        case 'completed':                     // job finished
        case 'cancel':                        // cancelled by driver / seeker
        case 'cancelled':                     // system-wide cancellation
            $driverData->update(['status' => 'available']);
            break;
    }
}
/* -------------------------------------------------- */

        return $this->sendResponse($success, 'Offer status updated successfully.');
    }
}
