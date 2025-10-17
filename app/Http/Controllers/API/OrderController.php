<?php

namespace App\Http\Controllers\API;

use App\Models\Evaluate;
use App\Models\Offer;
use App\Models\Car;
use App\Models\OfferStatus;
use App\Models\Order;
use App\Models\OrderAccountant;
use App\Models\OrderStatus;
use App\Models\User;
use App\Models\UserData;
use App\Models\OrdersInvites;
use App\Notifications\FcmPushNotification;
use App\Notifications\LocalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Http;

class OrderController extends BaseController
{
    public function getLimtedOrders(Request $request)
{
    $start = $request->start ?? 0;
    $limit = $request->limit ?? 10;

    $orders = Order::with(['user', 'car', 'serviceProvider', 'shipmentType', 'statuses', 'evaluate', 'paymentType', 'transaction', 'accountant'])
        ->when($request->status, function ($query) use ($request) {
            return $query->where('status', $request->status);
        })
        ->offset($start)
        ->limit($limit)
        ->latest()
        ->get();

    $success['count'] = $orders->count();
    $success['orders'] = $orders;

    return $this->sendResponse($success, 'Limited orders retrieved successfully.');
}

    public function getOrdersInvitesByOrderId($order_id)
{
    $invites = OrdersInvites::where('order_id', $order_id)
        ->with([
            'order' => function ($query) {
                $query->select('id', 'user_id', 'car_id', 'pick_up_address', 'drop_of_address', 'shipment_type_id', 'status');
            },
            'order.offers' // Ensure offers are related to the order
        ])
        ->get();

    if ($invites->isEmpty()) {
        return response()->json(['message' => 'No invites found for this order.'], 404);
    }

    // Get only users with type 'driver' and include their userData
    $drivers = User::whereIn('id', $invites->pluck('driver_id')->unique())
        ->where('type', 'driver') // ✅ Filter only drivers
        ->with(['userData' => function ($query) {
            $query->select('user_id', 'image', 'latitude', 'longitude', 'location', 'track_type')
                ->with('car', function ($carQuery) {
                    $carQuery->select('id', 'name_ar', 'name_en', 'image', 'weight');
                });
        }])
        ->get();

    // Format response
    $invitesWithOrders = $invites->map(function ($invite) {
        return [
            'id' => $invite->id,
            'user_id' => $invite->user_id,
            'driver_id' => $invite->driver_id,
            'order_id' => $invite->order_id,
            'order' => $invite->order, // ✅ Includes order details
            'offers' => $invite->order->offers ?? [] // ✅ Includes related offers
        ];
    });

    return response()->json([
        'message' => 'Orders invites retrieved successfully.',
        'drivers' => $drivers, // ✅ Includes only drivers with userData
        'data' => $invitesWithOrders
    ]);
}


public function getByDriverInvite(Request $request, $driver_id)
{

    $orders = Order::with([
            'user',
            'car',
            'serviceProvider',
            'shipmentType',
            'statuses',
            'evaluate',
            'paymentType',
            'transaction',
            'accountant',
            'offers'              // handy if you still want the offer list
        ])
        ->whereHas('invites', function ($q) use ($driver_id) {
            $q->where('driver_id', $driver_id);
        })
        ->when($request->status, function ($q) use ($request) {
            $q->where('status', $request->status);
        })
        ->latest()
        ->get();

    $success['count']  = $orders->count();
    $success['orders'] = $orders;

    return $this->sendResponse($success, 'Orders invited to driver retrieved successfully.');
}


    public function getByOrder($order_id)
{
    $invites = OrdersInvites::where('order_id', $order_id)
        ->with([
            'order' => function ($query) {
                $query->with(['user', 'car', 'shipmentType']);
            },
            'offers',
            'user' => function ($query) {
                $query->with(['userData']);
            },

        ])
        ->get();

    if ($invites->isEmpty()) {
        return response()->json(['message' => 'No invites found for this order.'], 404);
    }

    return response()->json([
        'message' => 'Orders invites retrieved successfully.',
        'data' => $invites
    ]);
}

public function getByUser($user_id)
{
    $invites = OrdersInvites::where('user_id', $user_id)
        ->with([
            'order' => function ($query) {
                $query->with(['user', 'car', 'shipmentType']);
            },
            'offers'
        ])
        ->get();

    if ($invites->isEmpty()) {
        return response()->json(['message' => 'No invites found for this user.'], 404);
    }

    return response()->json([
        'message' => 'Orders invites retrieved successfully.',
        'data' => $invites
    ]);
}

public function saveFcmToken(Request $request)
{
    $request->validate([
        'token' => 'required|string'
    ]);

    // Save token for logged-in user or first user (for testing)
    $user = auth()->user() ?? User::first();
    $user->fcm_token = $request->token;
    $user->save();

    return response()->json(['message' => 'FCM Token saved successfully!']);
}

public function sendWebNotification(Request $request)
{
    $firebaseServerKey = "AIzaSyDxTycXHWx6hMnpx90fSo2Y8SOFGXomA-w";

    if (!$firebaseServerKey) {
        return response()->json(['error' => 'Firebase Server Key not found'], 500);
    }

    // Get first user (for testing) or target a specific user
    $user = User::first();
    $fcmToken = $user->fcm_token;

    if (!$fcmToken) {
        return response()->json(['error' => 'FCM token not found'], 400);
    }

    $url = "https://fcm.googleapis.com/fcm/send";

    $notificationData = [
        'to' => $fcmToken,
        'notification' => [
            'title' => '🚀 Web Push Test',
            'body' => 'This is a test push notification for your web app!',
            'icon' => '/firebase-logo.png'
        ],
        'priority' => 'high'
    ];

    $response = Http::withHeaders([
        'Authorization' => 'key=' . $firebaseServerKey,
        'Content-Type'  => 'application/json',
    ])->post($url, $notificationData);

    return response()->json([
        'success' => true,
        'message' => 'Web push notification sent!',
        'response' => $response->json(),
    ]);
}


 /*   public function sendNotificatonToDrivers(Request $request, $id)
    {
      $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'drivers_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $msgs = [];
            foreach ($errors->all() as $ind => $message) {
                array_push($msgs, $message);
            }
            dd($errors);
            return $this->sendError('Validation Error.', $msgs);
        }

        $company = User::where('type', 'driverCompany')->find($id);
        $driversdata = UserData::where('company_id', $company->id)->get();
        $drivers_ids = [];
        $drivers = [];
        $count = 0;
        foreach ($driversdata as $dData) {
            foreach ((array)$request->drivers_id as $dri_id) {
                if ($dData->user_id == $dri_id) {
                    $driver = User::select('id', 'name', 'type', 'phone', 'active')->find($dri_id);
                    $driverid = $driver->id;
                    array_push($drivers, $driver);
                    array_push($drivers_ids, $driverid);
                    $count++;
                }
            }

        }
        $message = Lang::get('site.order_number') . ' ' . $request->order_id;
        $title = Lang::get('site.new_invited_by_company');
        foreach ($drivers as $driver) {
            $invited = OrdersInvites::where('user_id', $company->id)->where('driver_id', $driver->id, $request->order_id)->get();
            if ($invited->count() < 1) {
                $invite = OrdersInvites::create([
                    'user_id' => $company->id,
                    'driver_id' => $driver->id,
                    'order_id' => $request->order_id
                ]);
            }
            if (!empty($driver->fcm_token)) {
                 dd($driver);
                Notification::send($driver, new FcmPushNotification($title, $message, [$driver->fcm_token]));
            }
        }
        if ($count > 0) {
            $success['count'] = $count;
            $success['order_id'] = $request->order_id;
            $success['drivers'] = $drivers;
            return $this->sendResponse($success, 'Invites sended successfully .');
        } else {
            $success['count'] = 0;
            $success['drivers'] = [];
            return $this->sendResponse($success, 'Drivers not exists .');

        }
    }*/
    public function sendNotificatonToDrivers(Request $request, $id)
{
    // Validate request
    $validator = Validator::make($request->all(), [
        'order_id' => 'required|exists:orders,id',
        'drivers_id' => 'required|array',
        'drivers_id.*' => 'exists:users,id',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    // Find the company
    $company = User::where('type', 'driverCompany')->find($id);
    if (!$company) {
        return response()->json(['error' => 'Company not found'], 404);
    }

    // Get the drivers assigned to this company or where type is superadministrator or admin
    
    $drivers = User::whereIn('id', $request->drivers_id)->get();
$admins = User::whereIn('type', ['superadministrator', 'admin'])->get();
    $title = Lang::get('site.new_invited_by_company');
    $message = Lang::get('site.order_number') . ' ' . $request->order_id;
    $link = route('admin.orders.index', ['number' => $request->order_id]);

    $notifiedDrivers = 0;

    foreach ($drivers as $driver) {
        // Check if driver is already invited
        $existingInvite = OrdersInvites::where([
            'user_id' => $company->id,
            'driver_id' => $driver->id,
            'order_id' => $request->order_id,
        ])->exists();

        if (!$existingInvite) {
            // Create an invite record
            OrdersInvites::create([
                'user_id' => $company->id,
                'driver_id' => $driver->id,
                'order_id' => $request->order_id
            ]);

            // Prepare notification data
            $notificationData = [
                'title' => $title,
                'body' => $message,
                'target' => 'order',
                'link' => $link,
                'target_id' => $request->order_id,
                'sender' => $company->name, // Company sending the invite
            ];

            // Send the local database notification
       Notification::send($driver, new LocalNotification($notificationData));
            $notifiedDrivers++;
        }


                 $message2 = $title  . ' ' . $company->name. ' ' . $message ;
           //    $title = Lang::get('site.not_new_order');
                Notification::send($driver, new FcmPushNotification($title, $message2, [$driver->fcm_token]));

    }
 foreach ($admins as $admin) {     

            // Prepare notification data
            $notificationData = [
                'title' => $title,
                'body' => $message,
                'target' => 'order',
                'link' => $link,
                'target_id' => $request->order_id,
                'sender' => $company->name, // Company sending the invite
            ];
            // Send the local database notification
       Notification::send($admin, new LocalNotification($notificationData));
                 $message2 = $title  . ' ' . $company->name. ' ' . $message ;
           //    $title = Lang::get('site.not_new_order');
                Notification::send($admin, new FcmPushNotification($title, $message2, [$admin->fcm_token]));

    }
    if ($notifiedDrivers > 0) {
        return response()->json([
            'message' => 'Notifications sent successfully. '.$driver->fcm_token,
            'notified_count' => $notifiedDrivers
        ]);
    } else {
        return response()->json([
            'message' => 'No new notifications sent. Drivers may already be invited.'
        ]);
    }
}

    public function getByUserId($id)
    {
        $orders = Order::with(['user', 'car', 'serviceProvider', 'shipmentType', 'statuses', 'evaluate', 'paymentType', 'transaction', 'accountant'])->
        where('user_id', $id)->latest()->get();
        $success['count'] = $orders->count();
        $success['orders'] = $orders;
        return $this->sendResponse($success, 'Orders information.');
    }

    public function getByDriverId($id)
    {
        $orders = Order::with(['user', 'car', 'serviceProvider', 'shipmentType', 'statuses', 'evaluate', 'paymentType', 'transaction', 'accountant'])->where('service_provider', $id)->latest()->get();
        $success['count'] = $orders->count();
        $success['orders'] = $orders;
        return $this->sendResponse($success, 'Orders information.');
    }

    public function getByStatus(Request $request)
    {
        $orders = Order::with(['user', 'car', 'serviceProvider', 'shipmentType', 'statuses', 'evaluate', 'paymentType', 'transaction', 'accountant'])->when($request->status, function ($query) use ($request) {
            return $query->where('status', $request->status);
            // change track_id to car_id
        })->when($request->user_id, function ($query) use ($request) {
            return $query->where('user_id', $request->user_id);
        })->when($request->pick_up_address, function ($query) use ($request) {
            return $query->where('pick_up_address', 'LIKE', '%' . $request->pick_up_address . '%');
        })->when($request->drop_of_address, function ($query) use ($request) {
            return $query->where('drop_of_address', 'LIKE', '%' . $request->drop_of_address . '%');
        })->when($request->car_id, function ($query) use ($request) {
            return $query->where('car_id', $request->car_id);
        })->latest()->get();
        $success['count'] = $orders->count();
        $success['orders'] = $orders;

        return $this->sendResponse($success, 'orders information.');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'car_id' => 'required|exists:cars,id',
            'pick_up_address' => 'required',
            'pick_up_late' => 'required',
            'pick_up_long' => 'required',
            'drop_of_address' => 'required',
            'drop_of_late' => 'required',
            'drop_of_long' => 'required',
            'shipment_type_id' => 'required|exists:shipment_types,id',
            'shipment_details' => 'nullable|string',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'spoil_quickly' => 'required',
            'breakable' => 'required',
            'size' => 'required',
            'weight_ton' => 'required',
            'ton_price' => 'required',
            'shipping_date' => 'required',
            'status' => 'required',
            'desc' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $msgs = [];
            foreach ($errors->all() as $ind => $message) {
                array_push($msgs, $message);
            }
            return $this->sendError('Validation Error.', $msgs);
        }
        DB::beginTransaction();
        $order = Order::create([
            'user_id' => $request->user_id,
            'car_id' => $request->car_id,
            'pick_up_address' => $request->pick_up_address,
            'pick_up_late' => $request->pick_up_late,
            'pick_up_long' => $request->pick_up_long,
            'drop_of_address' => $request->drop_of_address,
            'drop_of_late' => $request->drop_of_late,
            'drop_of_long' => $request->drop_of_long,
            'shipment_type_id' => $request->shipment_type_id,
            'shipment_details' => $request->shipment_details,
            'payment_method_id' => $request->payment_method_id,
            'spoil_quickly' => $request->spoil_quickly,
            'breakable' => $request->breakable,
            'size' => $request->size,
            'weight_ton' => $request->weight_ton,
            'ton_price' => $request->ton_price,
            'total_price' => ($request->ton_price * $request->weight_ton),
            'shipping_date' => Carbon::parse($request->shipping_date)->format('Y-m-d H:i:s'),
            'status' => $request->status,
            'desc' => $request->desc,
            'notes' => $request->notes,
            'created_at' => Carbon::now()
        ]);
        $user = User::find($order->user_id);
        $object = [
    'order_id' => $order->id,
    'user_id'  => $order->user_id,
    'offer_id' => $order->offer_id,
    'status'   => $order->status,
];
        $data = [
            'title' => 'add',
            'body' => 'add_body',
            'target' => 'order',
            'object' => $object,
            'link' => route('admin.orders.index', ['number' => $order->id]),
            'target_id' => $order->id,
            'sender' => $user->name,
        ];
        //        $users = User::where('user_type','manage')->get();

        $users = User::where('type', 'superadministrator')->orWhere('type', 'admin')->orWhere('user_type', 'service_provider')->get();
        foreach ($users as $user) {
           Notification::send($user, new LocalNotification($data));
             if (!empty($user->fcm_token)) {
                 
                 $message = Lang::get('site.not_new_order_msg')  . ' ' . $order->id. ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' ' . auth()->user()->name;
               $title = Lang::get('site.not_new_order');
                Notification::send($user, new FcmPushNotification($title, $message, [$user->fcm_token]));
            }
        }
        DB::commit();
        $order1 = Order::with(['user', 'car', 'serviceProvider', 'shipmentType', 'statuses', 'evaluate', 'paymentType', 'transaction', 'accountant'])->find($order->id);

        $success['order'] = $order1;
        return $this->sendResponse($success, 'Order created successfully.');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'car_id' => 'required|exists:cars,id',
            'pick_up_address' => 'required',
            'pick_up_late' => 'required',
            'pick_up_long' => 'required',
            'drop_of_address' => 'required',
            'drop_of_late' => 'required',
            'drop_of_long' => 'required',
            'shipment_type_id' => 'required|exists:shipment_types,id',
            'shipment_details' => 'nullable|string',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'spoil_quickly' => 'required',
            'breakable' => 'required',
            'size' => 'required',
            'weight_ton' => 'required',
            'ton_price' => 'required',
            'shipping_date' => 'required',
            'status' => 'required',
            'desc' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            $msgs = [];
            foreach ($errors->all() as $ind => $message) {
                array_push($msgs, $message);
            }
            return $this->sendError('Validation Error.', $msgs);
        }
        $order = Order::where('user_id', $request->user_id)->find($id);
        if (!$order) {
            $msgs = ['Order not exists'];
            return $this->sendError('Data not found.', $msgs);
        }
        DB::beginTransaction();
        $order->update([
            'car_id' => $request->car_id,
            'pick_up_address' => $request->pick_up_address,
            'pick_up_late' => $request->pick_up_late,
            'pick_up_long' => $request->pick_up_long,
            'drop_of_address' => $request->drop_of_address,
            'drop_of_late' => $request->drop_of_late,
            'drop_of_long' => $request->drop_of_long,
            'shipment_type_id' => $request->shipment_type_id,
            'shipment_details' => $request->shipment_details,
            'payment_method_id' => $request->payment_method_id,
            'spoil_quickly' => $request->spoil_quickly,
            'breakable' => $request->breakable,
            'size' => $request->size,
            'weight_ton' => $request->weight_ton,
            'ton_price' => $request->ton_price,
            'total_price' => ($request->ton_price * $request->weight_ton),
            'shipping_date' => Carbon::parse($request->shipping_date)->format('Y-m-d H:i:s'),
            'status' => $request->status,
            'desc' => $request->desc,
            'notes' => $request->notes,
        ]);
        $user = User::find($order->user_id);
        $object = [
    'order_id' => $order->id,
    'user_id'  => $order->user_id,
    'offer_id' => $order->offer_id,
    'status'   => $order->status,
];
        $data = [
            'title' => 'edit',
            'body' => 'edit_body',
            'target' => 'order',
            'object' => $object,
            'link' => route('admin.orders.index', ['number' => $order->id]),
            'target_id' => $order->id,
            'sender' => $user->name,
        ];
     
        
        DB::commit();
        $order1 = Order::with(['user', 'car', 'serviceProvider', 'shipmentType', 'statuses', 'evaluate', 'paymentType', 'transaction', 'accountant'])->find($order->id);
$users = User::where('type', 'superadministrator')->orWhere('type', 'admin')->orWhere('user_type', 'service_provider')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
             if (!empty($user->fcm_token)) {
                $message = Lang::get('site.not_edit_order_msg') . ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' '. auth()->user()->name;
                $title = Lang::get('site.not_edit_order');
                Notification::send($user, new FcmPushNotification($title, $message, [$user->fcm_token]));
            }
        }
        $success['order'] = $order1;
        return $this->sendResponse($success, 'Order created successfully.');
    }

    public function show($id)
    {
        $order = Order::with(['car', 'shipmentType', 'statuses', 'evaluate', 'paymentType', 'transaction', 'accountant'])
            ->find($id);


        if (!$order) {
            $msgs = ['Order not exists'];
            return $this->sendError('Data not found.', $msgs);
        }
        $user = User::find($order->user_id);
        $userData = UserData::where('user_id', $user->id)->get()->first();

        $user['image'] = $userData->image;
        $avg = Evaluate::with(['user'])->where('user2_id', $user->id)->avg('rate');
        $eval_count = Evaluate::with(['user'])->where('user2_id', $user->id)->count();
        $user['rate_avg'] = number_format($avg, 2);
        $user['evaluates_count'] = $eval_count;
        //$order['service_provider']=null;
        if ($order->service_provider) {
            $service_provider = User::find($order->service_provider);
            $service_providerData = UserData::where('user_id', $service_provider->id)->get()->first();
            $service_provider['image'] = $service_providerData->image;
            $service_provider['driver_id'] = $service_provider->id;
            $service_provider['driver_image'] = $service_providerData->image;
            $service_provider['driver_name'] = $service_provider->name;
            $avg1 = Evaluate::with(['user'])->where('user2_id', $service_provider->id)->avg('rate');
            $service_provider['rate_avg'] = number_format($avg1, 2);
            $eval_count1 = Evaluate::with(['user'])->where('user2_id', $service_provider->id)->count();
            $service_provider['evaluates_count'] = $eval_count1;
            if (auth()->user()->type == 'driverCompany') {
                $userAssigned = OrdersInvites::where('order_id', $order->id)->get();
                $DriversAssigned = [];
                foreach ($userAssigned as $userass) {
                    $driv = User::select('id', 'name', 'phone', 'type', 'active')->find($userass->driver_id);
                    $drivData = UserData::where('user_id', $userass->driver_id)->get()->first();
                    $driv['image'] = $userData->image;
                    $driver_car = Car::find($drivData->track_type);
                    if ($driver_car) {
                        $driv['car_id'] = $driver_car->id;
                        $driv['car_name_ar'] = $driver_car->name_ar;
                        $driv['car_name_en'] = $driver_car->name_en;
                        $driv['car_weight'] = $driver_car->weight;
                        $driv['car_image'] = $driver_car->car_image;
                    } else {
                        $driv['car_id'] = null;
                        $driv['car_name_ar'] = null;
                        $driv['car_name_en'] = null;
                        $driv['car_weight'] = null;
                        $driv['car_image'] = null;
                    }
                    $avg = Evaluate::with(['user'])->where('user_id', $userass->driver_id)->avg('rate');
                    $eval_count = Evaluate::with(['user'])->where('user_id', $userass->driver_id)->count();
                    $driver_offer = Offer::where('driver_id', $userass->driver_id)->get()->first();
                    if ($driver_offer) {
                        $driv['vat'] = $driver_offer->vat;
                        $driv['ton_price'] = number_format($driver_offer->ton_price, 2);
                        $driv['price'] = number_format($driver_offer->price, 2);
                        $driv['sub_total'] = number_format($driver_offer->sub_total, 2);
                    } else {
                        $driv['vat'] = 0;
                        $driv['ton_price'] = 0.00;
                        $driv['price'] = 0.00;
                        $driv['sub_total'] = 0.00;
                    }
                    $driv['rate_avg'] = number_format($avg, 2);

                    $driv['evaluates_count'] = $eval_count;
                    array_push($DriversAssigned, $driv);
                }
                if ($userAssigned) {
                    $order['DriversAssigned'] = $DriversAssigned;
                }
            }
            $order['user'] = $user;
            $order['service_provider'] = $service_provider;
        }
        $offer = Offer::where('order_id', $order->id)->where('driver_id', $order->service_provider)
            ->where('status', '!=', 'pending')->first();
        if (isset($offer)) {
            $success['vat'] = $offer->vat;
            $success['shipping_price'] = $offer->price;
            $success['subtotal'] = $offer->sub_total;
        } else {
            $success['vat'] = 0;
            $success['shipping_price'] = 0;
            $success['subtotal'] = 0;
        }
        $success['order'] = $order;


        return $this->sendResponse($success, 'Order information.');
    }

    /*public function changeStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'order_id' => 'required|exists:orders,id',
            'status' => 'required',
            'distance' => 'nullable|string',
            'change_by' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $msgs = [];
            foreach ($errors->all() as $ind => $message) {
                array_push($msgs, $message);
            }
            return $this->sendError('Validation Error.', $msgs);
        }
        $order = Order::find($id);
        if (!$order) {
            $msgs = ['Order not exists'];
            return $this->sendError('Data not found.', $msgs);
        }
        DB::beginTransaction();
        $order->update([
            'status' => $request->status,
        ]);
        $order = Order::with(['accountant', 'statuses'])->find($id);
        if ($order->status != 'cancel') {
            OrderStatus::create([
                'user_id' => $request->user_id,
                'order_id' => $order->id,
                'distance' => $request->distance,
                'status' => $order->status,
                'change_by' => $request->change_by,
            ]);
            $offer = Offer::find($order->offer_id);
            if ($offer) {
                $offer->update(['status' => $order->status]);
                OfferStatus::create([
                    'user_id' => $request->user_id,
                    'offer_id' => $offer->id,
                    'status' => $order->status,
                    'change_by' => $request->change_by,
                ]);
            }

        }
        if ($order->status == 'cancel') {
            $accountant = OrderAccountant::where('order_id', $order->id)->get()->first();
            $des_Cost = $accountant->diesel_cost * $request->distance;
            $accountant->update(['fine' => $accountant->fine + $des_Cost]);
            $driver = UserData::where('user_id', $order->service_provider)->get()->first();
            $pending_balance = $driver->pending_balance - $accountant->service_provider_amount + $des_Cost;
            $driver->update(['pending_balance' => $pending_balance]);
            $user = UserData::where('user_id', $order->user_id)->get()->first();
            if ($user->type == 'factory') {
                $outstanding_balance = $user->outstanding_balance - $order->total_price + $accountant->fine - $des_Cost;
                $user->update(['outstanding_balance' => $outstanding_balance]);
                $success['factory'] = $user;
                return $this->sendResponse($success, 'Order status created successfully.');
            } elseif ($user->type == 'user') {
                $balance = $user->balance - $order->total_price + $accountant->fine - $des_Cost;
                $user->update(['$balance' => $user->balance + $balance, 'outstanding_balance' => $user->outstanding_balance - $balance]);
            }
            $offer = Offer::find($order->offer_id);
            if ($offer) {
                $offer->update(['status' => $order->status]);
                OfferStatus::create([
                    'user_id' => $request->user_id,
                    'offer_id' => $offer->id,
                    'status' => $order->status,
                    'change_by' => $request->change_by,
                ]);
            }
        }

        $user = User::find($order->user_id);

        $message = Lang::get('site.not_approve_order_msg') . ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user') . ' ' . auth()->user()->name;
        $title = Lang::get('site.not_approve_order');
        if ($order->status == 'approve') {
            $message = Lang::get('site.not_approve_order_msg') . ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user') . ' ' . auth()->user()->name;
            $title = Lang::get('site.not_approve_order');

        } elseif ($order->status == 'pick_up') {
            $message = Lang::get('site.not_pick_up_order_msg') . ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user') . ' ' . auth()->user()->name;
            $title = Lang::get('site.not_pick_up_order');
        } elseif ($order->status == 'delivered') {
            $message = Lang::get('site.not_delivered_order_msg') . ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user') . ' ' . auth()->user()->name;
            $title = Lang::get('site.not_delivered_order');
            $orderstatus = OrderStatus::where('order_id', $order->id)->get();
            $pick_up = null;
            $delivered = null;
            foreach ($orderstatus as $status) {
                if ($status->status == 'pick_up') {
                    $pick_up = $status->created_at;
                }
                if ($status->status == 'delivered') {
                    $delivered = $status->created_at;
                }
                $workHours = ceil((int)(strtotime($delivered) - strtotime($pick_up)) / (60 * 60)) . '';
                $driver = UserData::where('user_id', $order->service_provider)->get()->first();
                $driver->update(['works_hours' => $workHours]);
            }
        } elseif ($order->status == 'cancel') {
            $message = Lang::get('site.not_cancel_order_msg') . ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user') . ' ' . auth()->user()->name;
            $title = Lang::get('site.not_cancel_order');
        } elseif ($order->status == 'pending') {
            $message = Lang::get('site.not_pend_order_msg') . ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user') . ' ' . auth()->user()->name;
            $title = Lang::get('site.not_pend_order');
        } elseif ($order->status == 'complete') {
            $message = Lang::get('site.not_complete_order_msg') . ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user') . ' ' . auth()->user()->name;
            $title = Lang::get('site.not_complete_order');
            $driver = UserData::where('user_id', $order->service_provider)->get()->first();
            $driver->update(['status' => 'available']);
        }
        $data = [
            'title' => $request->stauts,
            'body' => 'add_body',
            'target' => 'order',
            'link' => route('admin.orders.index', ['number' => $order->id]),
            'target_id' => $order->id,
            'sender' => $user->name,
        ];
        $users = User::where('user_type', 'manage')->where('id', $order->user_id)->get();
        $offer = Offer::where('order_id', $order->id)->where('status', 'approve')->orWhere('status', 'pick_up')->orWhere('status', 'delivered')->first();
        $provider = User::find($order->service_provider);
        if (!empty($provider->fcm_token)) {
            Notification::send($provider, new FcmPushNotification($title, $message, [$provider->fcm_token]));
        }
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
            // if (!empty($user->fcm_token) && $user->id != auth()->user()->id) {
            //     Notification::send($user, new FcmPushNotification($title, $message, [$user->fcm_token]));
            // }
        }
        DB::commit();
        $success['order'] = $order;
        return $this->sendResponse($success, 'Order status created successfully.');
    }*/
    public function changeStatus(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|exists:users,id',
        'order_id' => 'required|exists:orders,id',
        'status' => 'required',
        'distance' => 'nullable|string',
        'change_by' => 'nullable|string',
        'notes' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        $msgs = $validator->errors()->all();
        return $this->sendError('Validation Error.', $msgs);
    }

    $order = Order::find($id);
    if (!$order) {
        return $this->sendError('Data not found.', ['Order not exists']);
    }

    DB::beginTransaction();
    $order->update(['status' => $request->status]);
    $order = Order::with(['accountant', 'statuses'])->find($id);

    if ($order->status != 'cancel') {
        OrderStatus::create([
            'user_id' => $request->user_id,
            'order_id' => $order->id,
            'distance' => $request->distance,
            'status' => $order->status,
            'change_by' => $request->change_by,
        ]);

        $offer = Offer::find($order->offer_id);
                /* -------- NEW: update driver availability -------- */
$driverData = UserData::where('user_id', $offer->driver_id)->first();
if ($driverData) {
    switch ($order->status) {
        case 'approve':                       // offer accepted
            $driverData->update(['status' => 'busy']);
            break;

        case 'pick_up':                       // driver en-route / loading
        case 'delivered':                      // still handling shipment
            $driverData->update(['status' => 'in_Shipment']);
            break;

        case 'completed':
            case 'complete':  // job finished
        case 'cancel':
            case 'cancelled': // cancelled by driver / seeker
            $driverData->update(['status' => 'available']);
            break;
    }
        if ($offer) {
            $offer->update(['status' => $order->status]);
            OfferStatus::create([
                'user_id' => $offer->driver_id,
                'offer_id' => $offer->id,
                'status' => $order->status,
                'change_by' => $offer->driver_id,
            ]);
        }
    }

    if ($order->status === 'approve') {
        $chatRoom = \App\Models\ChatRoom::updateOrCreate(
            ['order_id' => $order->id],
            [
                'title' => 'Order # ' . $order->id,
                'description' => 'Chat Room For Order Number : ' . $order->id
            ]
        );

        $chatRoom->join($order->user_id);
        if ($order->service_provider) {
            $chatRoom->join($order->service_provider);
        }
    }

    if ($order->status == 'cancel') {
        $accountant = OrderAccountant::where('order_id', $order->id)->first();
        $des_Cost = $accountant->diesel_cost * $request->distance;
        $accountant->update(['fine' => $accountant->fine + $des_Cost]);
        $driver = UserData::where('user_id', $order->service_provider)->first();
        $pending_balance = $driver->pending_balance - $accountant->service_provider_amount + $des_Cost;
        $driver->update(['pending_balance' => $pending_balance]);

        $user = UserData::where('user_id', $order->user_id)->first();
        if ($user->type == 'factory') {
            $outstanding_balance = $user->outstanding_balance - $order->total_price + $accountant->fine - $des_Cost;
            $user->update(['outstanding_balance' => $outstanding_balance]);
        } elseif ($user->type == 'user') {
            $balance = $user->balance - $order->total_price + $accountant->fine - $des_Cost;
            $user->update(['balance' => $user->balance + $balance, 'outstanding_balance' => $user->outstanding_balance - $balance]);
        }
    }

    // Notification logic
    $user = User::find($order->user_id);
    // Build title/message keys first so we can localize for multiple locales
    $titleKey = '';
    $messageKey = '';
    switch ($order->status) {
        case 'approve':
            $messageKey = 'not_approve_offer_msg';
            $titleKey = 'not_approve_offer';
            break;
        case 'pick_up':
            $messageKey = 'not_pick_up_order_msg';
            $titleKey = 'not_pick_up_order';
            break;
        case 'delivered':
            $messageKey = 'not_delivered_order_msg';
            $titleKey = 'not_delivered_order';
            break;
        case 'complete':
            $messageKey = 'not_complete_order_msg';
            $titleKey = 'not_complete_order';
            UserData::where('user_id', $order->service_provider)->update(['status' => 'available']);
            break;
        case 'cancel':
            $messageKey = 'not_cancel_order_msg';
            $titleKey = 'not_cancel_order';
            break;
        case 'pending':
            $messageKey = 'not_pend_order_msg';
            $titleKey = 'not_pend_order';
            break;
    }
    // Resolve current-locale strings for FCM/body composition
    $title = Lang::get('site.' . $titleKey);
    $message = Lang::get('site.' . $messageKey);
    $message .= ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user') . ' ' . auth()->user()->name;

// Common notification object payload
$object = [
    'order_id' => $order->id,
    'user_id'  => $order->user_id,
    'offer_id' => $order->offer_id,
    'status'   => $order->status,
];

// Keys for admin dashboard notifications (rendered via @lang('site.{key}'))
$statusKey = $order->status; // e.g. approve, pick_up, delivered, complete, cancel, pending
$bodyKey   = $messageKey ?: 'add_body'; // Prefer specific message key when available

// Admin/superadmin/service-provider (dashboard) notification payload
$dataAdmin = [
    'title'      => $statusKey, // resolved in Blade via @lang('site.' . title)
    'body'       => $bodyKey,   // optional body key
    'target'     => 'order',
    'object'     => $object,
    'link'       => route('admin.orders.index', ['number' => $order->id]),
    'target_id'  => $order->id,
    'sender'     => $user->name ?? null,
];

// Frontend users (seeker/provider app) should get localized strings directly
$dataFront = [
    'title'      => [
        'ar' => Lang::get('site.' . $titleKey, [], 'ar'),
        'en' => Lang::get('site.' . $titleKey, [], 'en'),
    ],
    'body'       => [
        'ar' => Lang::get('site.' . $messageKey, [], 'ar'),
        'en' => Lang::get('site.' . $messageKey, [], 'en'),
    ],
    'target'     => 'order',
    'object'     => $object,
    'link'       => route('admin.orders.index', ['number' => $order->id]),
    'target_id'  => $order->id,
    'sender'     => $user->name ?? null,
];



   $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
    $provider = User::find($order->driver_id);
     $user_sekker = User::find($order->user_id);
    if (!empty($provider->fcm_token)) {
        Notification::send($provider, new FcmPushNotification($title, $message, [$provider->fcm_token]));
        // Notification::send("fMYK1Y4aImtQRe5Tqhru6A:APA91bGaUdFv2G_U5nuiHhjrWfrzpMrKgQ2sxPgh8NRy1-c56KWwrqaOm4GAQtFwgJuQ2-L4gVcO39b8TGIXhdxd96AMI4N4FkcFyOFkGix-sqw_KL4tzZg", new FcmPushNotification($title, $message, ["fMYK1Y4aImtQRe5Tqhru6A:APA91bGaUdFv2G_U5nuiHhjrWfrzpMrKgQ2sxPgh8NRy1-c56KWwrqaOm4GAQtFwgJuQ2-L4gVcO39b8TGIXhdxd96AMI4N4FkcFyOFkGix-sqw_KL4tzZg"]));
    }
    // Provider gets localized payload as well
    Notification::send($provider, new LocalNotification($dataFront));
    if (!empty($user_sekker->fcm_token)) {
        Notification::send($user_sekker, new FcmPushNotification($title, $message, [$user_sekker->fcm_token]));
         // Notification::send("fMYK1Y4aImtQRe5Tqhru6A:APA91bGaUdFv2G_U5nuiHhjrWfrzpMrKgQ2sxPgh8NRy1-c56KWwrqaOm4GAQtFwgJuQ2-L4gVcO39b8TGIXhdxd96AMI4N4FkcFyOFkGix-sqw_KL4tzZg", new FcmPushNotification($title, $message, ["fMYK1Y4aImtQRe5Tqhru6A:APA91bGaUdFv2G_U5nuiHhjrWfrzpMrKgQ2sxPgh8NRy1-c56KWwrqaOm4GAQtFwgJuQ2-L4gVcO39b8TGIXhdxd96AMI4N4FkcFyOFkGix-sqw_KL4tzZg"]));
         // Frontend user (seeker) gets localized payload
         Notification::send($user_sekker, new LocalNotification($dataFront));
    }
    foreach ($users as $user) {
          Notification::send($user, new FcmPushNotification($title, $message, [$user->fcm_token]));
        // Admin/superadmin (dashboard) gets translation-key payload
        Notification::send($user, new LocalNotification($dataAdmin));
    }

    DB::commit();
    return $this->sendResponse(['order' => $order], 'Order status updated successfully.');
}

}
    public function Tracking_order(Order $order)
    {
        // $result=$order->get('pick_up_address','pick_up_late','pick_up_long','drop_of_address','drop_of_late','drop_of_long');
        $result['pick_up_address'] = $order->pick_up_address;
        $result['pick_up_late'] = $order->pick_up_late;
        $result['pick_up_long'] = $order->pick_up_long;
        $result['drop_of_address'] = $order->drop_of_address;
        $result['drop_of_late'] = $order->drop_of_late;
        $result['drop_of_long'] = $order->drop_of_long;

        return $this->sendResponse($result, '');

    }

}
