<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use App\Mail\DynamicEmail;
use App\Models\Country;
use App\Models\Message;
use App\Models\Offer;
use App\Models\SupportCenter;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\FcmPushNotification;
use App\Services\FCMService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Pnlinh\InfobipSms\Facades\InfobipSms;
use App\Models\Car;
use App\Models\ShipmentType;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_admin']);
    }
    public function index(Request $request)
    {
        if (session('success')) {
            toast(session('success'), 'success');
        }

        $startDate = $request->start_date
            ? Carbon::parse($request->start_date)->startOfDay()
            : now()->subDays(29)->startOfDay();
        $endDate = $request->end_date
            ? Carbon::parse($request->end_date)->endOfDay()
            : now()->endOfDay();
        $dateRange = [$startDate, $endDate];

        $ordersQuery = Order::whereBetween('created_at', $dateRange);
        $ordersConut = (clone $ordersQuery)->count();
        $orders = (clone $ordersQuery)->with([
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
                'statuses',
            ])->latest()->take(5)->get();

        $offersQuery = Offer::whereBetween('created_at', $dateRange);
        $offers = (clone $offersQuery)->latest()->take(5)->get();
        $completedOffersQuery = (clone $offersQuery)->where('status', 'complete');
        $offers_price = (clone $completedOffersQuery)->sum('price');
        $offers_total = (clone $completedOffersQuery)->sum('sub_total');
        $total_tax = $offers_total - $offers_price;

        $calculateEarnings = function (Carbon $rangeStart, Carbon $rangeEnd) {
            $total_income = 0;
            $income = 0;
            $profit = 0;
            $total_profit = 0;

            $transactions = Transaction::with(['order.serviceProvider', 'order.accountant', 'payTransaction'])
                ->whereBetween('created_at', [$rangeStart, $rangeEnd])
                ->latest()
                ->orderBy('id', 'desc')
                ->get();

            foreach ($transactions as $tran) {
                if (!$tran->payTransaction) {
                    continue;
                }
                $amount = (float) $tran->payTransaction->amount;
                $total_income += $amount;

                $accountant = $tran->order?->accountant;
                $serviceProvider = $tran->order?->serviceProvider;
                if (!$serviceProvider || !$accountant) {
                    continue;
                }

                $commission = (float) ($accountant->service_provider_commission ?? 0);
                $income += ($amount * $commission) / 100;

                $serviceProviderAmount = (float) ($accountant->service_provider_amount ?? 0);
                $total_profit += $amount - $serviceProviderAmount;

                $fees = (float) ($accountant->service_seeker_fee ?? 0);
                $expenses = (float) ($accountant->expenses ?? 0);
                $operatingCosts = (float) ($accountant->operating_costs ?? 0);
                $profit += ($amount - $serviceProviderAmount) - ($fees + $expenses + $operatingCosts);
            }

            return [
                'income' => $income,
                'total_income' => $total_income,
                'profit' => $profit,
                'total_profit' => $total_profit,
            ];
        };
        $earn = $calculateEarnings($startDate, $endDate);

        $customersCount = User::where('type', 'user')->whereBetween('created_at', $dateRange)->count();
        $factoriesCount = User::where('type', 'factory')->whereBetween('created_at', $dateRange)->count();
        $driversCount = User::where('type', 'driver')->whereBetween('created_at', $dateRange)->count();
        $driversCompanyCount = User::where('type', 'driverCompany')->whereBetween('created_at', $dateRange)->count();
        $pendingOrdersCount = Order::whereBetween('created_at', $dateRange)->where('status', 'pending')->count();
        $approveOrdersCount = Order::whereBetween('created_at', $dateRange)->whereIn('status', ['approve', 'wait_accept'])->count();
        $completeOrderCount = Order::whereBetween('created_at', $dateRange)->where('status', 'complete')->count();
        $cancelOrderCount = Order::whereBetween('created_at', $dateRange)->where('status', 'cancel')->count();
        $online_drivers = User::where('type', 'driver')->where('online', 1)->count();
        
        /*-------------------*/
        
        $rangeDays = $startDate->diffInDays($endDate) + 1;
        $previous_end = $startDate->copy()->subDay()->endOfDay();
        $previous_start = $previous_end->copy()->subDays($rangeDays - 1)->startOfDay();

        $prev_customers = User::where('type', 'user')->whereBetween('created_at', [$previous_start, $previous_end])->count();
        $prev_factories = User::where('type', 'factory')->whereBetween('created_at', [$previous_start, $previous_end])->count();
        $prev_drivers = User::where('type', 'driver')->whereBetween('created_at', [$previous_start, $previous_end])->count();
        $prev_companies = User::where('type', 'driverCompany')->whereBetween('created_at', [$previous_start, $previous_end])->count();
        $prev_orders = Order::whereBetween('created_at', [$previous_start, $previous_end])->count();
        $prev_pending = Order::where('status', 'pending')->whereBetween('created_at', [$previous_start, $previous_end])->count();
        $prev_approve = Order::whereIn('status', ['approve', 'wait_accept'])->whereBetween('created_at', [$previous_start, $previous_end])->count();
        $prev_complete = Order::where('status', 'complete')->whereBetween('created_at', [$previous_start, $previous_end])->count();
        $prev_cancel = Order::where('status', 'cancel')->whereBetween('created_at', [$previous_start, $previous_end])->count();
        $prev_online = User::where('type', 'driver')->where('online', 1)->whereBetween('updated_at', [$previous_start, $previous_end])->count();
        $prev_earn = $calculateEarnings($previous_start, $previous_end);

        $prev_completed_offers = Offer::whereBetween('created_at', [$previous_start, $previous_end])
            ->where('status', 'complete');
        $prev_offers_price = (clone $prev_completed_offers)->sum('price');
        $prev_offers_total = (clone $prev_completed_offers)->sum('sub_total');
        $prev_total_tax = $prev_offers_total - $prev_offers_price;

        $getPercentageChange = static function ($current, $previous) {
            if ($previous == 0) {
                return $current > 0 ? 100 : 0;
            }
            return round((($current - $previous) / $previous) * 100, 2);
        };
$stats_change = [
    'profit' => $getPercentageChange($earn['profit'], $prev_earn['profit']),
    'tax' =>  $getPercentageChange($total_tax, $prev_total_tax),
    'customers' =>  $getPercentageChange($customersCount, $prev_customers),
    'factories' => $getPercentageChange($factoriesCount, $prev_factories),
    'drivers' =>  $getPercentageChange($driversCount, $prev_drivers),
    'companies' =>  $getPercentageChange($driversCompanyCount, $prev_companies),
    'orders' =>  $getPercentageChange($ordersConut, $prev_orders),
    'pendingOrders' =>  $getPercentageChange($pendingOrdersCount, $prev_pending),
    'approvedOrders' => $getPercentageChange($approveOrdersCount, $prev_approve),
    'completeOrders' => $getPercentageChange($completeOrderCount, $prev_complete),
    'cancelOrders' =>  $getPercentageChange($cancelOrderCount, $prev_cancel),
    'onlineDrivers' =>  $getPercentageChange($online_drivers, $prev_online),
];

/*-----------------------*/
        
      
        
        return view('admin.index',['orders'=> $orders,'offers'=>$offers,'earn'=>$earn,'total_tax'=>$total_tax,'online_drivers'=>$online_drivers,'ordersConut'=>$ordersConut,'customersCount'=>$customersCount,
        'factoriesCount'=>$factoriesCount,'driversCount'=>$driversCount,'driversCompanyCount'=>$driversCompanyCount,
        'pendingOrdersCount'=>$pendingOrdersCount,'approveOrdersCount'=>$approveOrdersCount,'completeOrderCount'=>$completeOrderCount,'cancelOrderCount'=>$cancelOrderCount,
        'stats_change'=>$stats_change
        ]);
    }
    public function oldsearch(Request $request){
        $users=[];
        if($request->search !=''){
        $users=User::where('type','!=','superadministrator')->whereIn('type',['user','driver','factory','driverCompany'])->
        where('name', 'like', '%' . $request->search . '%')->orWhere('phone', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')->paginate(10,['*'],'users');
        }
        $user_id = User::where('name', 'like', '%' . $request->search . '%')->get()->first();  /*add by mohammed v2*/
        $car_id = Car::where('name_en', 'like', '%' . $request->search . '%')->orWhere('name_ar', 'like', '%' . $request->search . '%')->get()->first(); /*add by mohammed v2*/
        $shipment_type_id = ShipmentType::where('name_en', 'like', '%' . $request->search . '%')->orWhere('name_ar', 'like', '%' . $request->search . '%')->get()->first(); /*add by mohammed v2*/
            $orders = Order::where('id',  $request->search)
                        ->orWhere('user_id', @$user_id->id)
                        ->orWhere('offer_id', @$user_id->id)
                        ->orWhere('car_id', @$car_id->id )
                        ->orWhere('shipment_type_id', @$shipment_type_id->id)
                        ->orWhere('size', $request->search)
                        ->orWhere('ton_price', $request->search)
                        ->orWhere('total_price', $request->search)
                        ->orWhere('weight_ton', $request->search)
            ->with(['car', 'user', 'shipmentType',  'evaluate'])->select()->latest()->orderBy('id', 'desc')->paginate(10,['*'],'orders');
        return view('admin.search',['users'=>$users,'orders'=>$orders]);
    }
    public function search(Request $request)
    {
        $users = [];
        $orders = [];

        if ($request->search != '') {
            $users = User::where('type', '!=', 'superadministrator')
                ->whereIn('type', ['user', 'driver', 'factory', 'driverCompany'])
                ->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('phone', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%');
                })
                ->paginate(10, ['*'], 'users');
        }

        $user = User::where('name', 'like', '%' . $request->search . '%')->first();
        $car = Car::where(function ($query) use ($request) {
            $query->where('name_en', 'like', '%' . $request->search . '%')
                ->orWhere('name_ar', 'like', '%' . $request->search . '%');
        })->first();
        $shipmentType = ShipmentType::where(function ($query) use ($request) {
            $query->where('name_en', 'like', '%' . $request->search . '%')
                ->orWhere('name_ar', 'like', '%' . $request->search . '%');
        })->first();

        if (!empty($user) || !empty($car) || !empty($shipmentType)) {
            $orderQuery = Order::where('size', $request->search)
                ->orWhere('ton_price', $request->search)
                ->orWhere('total_price', $request->search)
                ->orWhere('weight_ton', $request->search);

            if (!empty($user)) {
                $orderQuery->orWhere('user_id', $user->id);
            }

            if (!empty($car)) {
                $orderQuery->orWhere('car_id', $car->id);
            }

            if (!empty($shipmentType)) {
                $orderQuery->orWhere('shipment_type_id', $shipmentType->id);
            }

            $orders = $orderQuery->with(['car', 'user', 'shipmentType', 'evaluate'])
                ->select()
                ->latest()
                ->orderBy('id', 'desc')
                ->paginate(10, ['*'], 'orders');
        }

        return view('admin.search', ['users' => $users, 'orders' => $orders]);
    } /*add by mohammed*/

    public function clear_views()
    {
        Artisan::call('view:clear');
        return redirect()->back()->with("success", __('site.views_cleared'));
    }
    public function clear_cache()
    {
        Artisan::call('cache:clear');
        return redirect()->back()->with("success", __('site.cache_cleared'));
    }
    public function clear_routes()
    {
        Artisan::call('route:clear');

        return redirect()->back()->with("success", __('site.routes_cleared'));
    }
    public function clear_config()
    {
        Artisan::call('config:clear');
        return redirect()->back()->with("success", __('site.config_cleared'));
    }
    public function clear_optimize()
    {
        Artisan::call('optimize:clear');
        return redirect()->back()->with("success", __('site.optimize_cleared'));
    }
    public function send_mail()
    {
        if (session('success')) {
            toast(session('success'), 'success');
        }
        $messages = Message::latest()->where('type', 'Email')->paginate(10);
        return view('admin.emails.mail',['messages'=> $messages]);
    }
    public function sendMail(Request $request)
    {
        $request->validate([
            'email' =>'email|required',
            'subject' =>'string|required',
            'cc'      =>  'string|required',
            'bcc'     =>  'string|required',
            'message' => 'string|required'
        ]);
        $toEmail    =   $request->email;
        $data       =   array(
            "site_link" =>  setting('site_link'),
            "subject"   =>  $request->subject,
            "message"   =>   $request->message,
            "view"      =>  'admin.emails.sendMail',
        );
        $msgData = [
            'sender_id' => auth()->user()->id,
            'receiver_id' => null,
            'message' => $request->message,
            'type' => 'Email',
            'notes' => $request->email
        ];
        // pass dynamic message to mail class
        Mail::to($toEmail)->send(new DynamicEmail($data));
        $msgData['status'] = 'complete';
        Message::create($msgData);

        session()->flash("success", __('site.mail_sended'));
        return redirect()->route('admin.messages.customer_messages');
    }
    public function send_sms()
    {
        if (session('success')) {
            toast(session('success'), 'success');
        }
        $sms=Message::latest()->where('type', 'SMS')->orWhere('type', 'Sms for otp')->paginate(10);
        dd($sms);
        return view('admin.emails.sms',['sms'=>$sms]);
    }
    public function sendSms(Request $request)
    {
        $request->validate([
            'phone' => 'string|required',
            'message' => 'string|required'
        ]);
        $msgData = [
            'sender_id' => auth()->user()->id,
            'receiver_id' => null,
            'message' => $request->message,
            'type' => 'SMS',
            'notes'=>$request->phone
        ];
        // Send to one number
        //
        if($request->users_list !=null){
          $users_list = json_encode( $request->users_list);
        if(!empty($users_list)/*  && count($request->users_list) > 0 */)
        {
            foreach(json_decode($users_list,true) as $userType){
                $type = $userType;
                $user=new User();
                if($userType=='user' || $userType=='factory' || $userType=='driver' || $userType== 'driverCompany') {
                    $users = User::where('type', $type)->get();
                }
                elseif($userType=='vip_user'){
                    $users = User::where('type', 'user')->with('userData',function($q){
                        return $q->where('vip',1)->get();
                    })->get();
                }
                elseif($userType== 'vip_factory'){
                    $users = User::where('type', 'factory')->with('userData', function ($q) {
                        return $q->where('vip', 1)->get();
                    })->get();
                }
                elseif($userType== 'vip_driver'){
                    $users = User::where('type', 'driver')->with('userData', function ($q) {
                        return $q->where('vip', 1)->get();
                    })->get();
                }
                elseif($userType== 'vip_driverCompany'){
                    $users = User::where('type', 'driverCompany')->with('userData', function ($q) {
                        return $q->where('vip', 1)->get();
                    })->get();
                }
             //   Log::info($userType);
                 foreach($users as $index=>$user) {
                    //Log::info($user);
               $msgData = [
                    'sender_id' => auth()->user()->id,
                    'receiver_id' => null,
                    'message' => $request->message,
                    'type' => 'Email',
                    'to' => $user->phone
                ];
            $response = InfobipSms::send($user->phone, $request->message);
                Log::info(__('site.message_sended') .' '. $request->subject. '   ' . $user->phone);
               }
            }
        }
        }
        $success['message'] = 'Otp Verification';

        // if ($response[0] != 200) {
        //     $msgData['status'] = 'wait';
        //     return $this->sendResponse($success, 'Otp not sended,Plase try angin.');
        // }

        // $msgData['status'] = $response[1]->messages[0]->status->groupName;

       // $msgData['status'] = 'complete';
        Message::create($msgData);
        $response = InfobipSms::send($request->phone, $msgData['message']);
        session()->flash("success", __('site.message_sended'));
        return redirect()->route('admin.messages.customer_messages');

    }
    public function customerMessages(Request $request)
    {
        if (session('success')) {
            toast(session('success'), 'success');
        }
        $messages=SupportCenter::when($request->title, function ($query) use ($request) {
            return $query->where('title', 'like', '%' . $request->title . '%');
        })->when($request->message, function ($query) use ($request) {
            return $query->where('message', 'like', '%' . $request->message . '%');
        })->when($request->user_id, function ($query) use ($request) {
            return $query->where('user_id', $request->user_id );
        })->when($request->replay_by, function ($query) use ($request) {
            return $query->where('replay_by', $request->replay_by);
        })->latest()->paginate(10);
        $users=User::select('id','name')->where('type','user')->OrWhere('type', 'factory')
        ->OrWhere('type', 'driver')->OrWhere('type', 'driverCompany')->orderBy('name','ASC')->get();
        $replaiers = User::select('id', 'name')->where('type', 'superadministrator')->OrWhere('type', 'admin')
        ->OrWhere('type', 'emp')->orderBy('name', 'ASC')->paginate(10, ['*'], 'support');

        $sms = Message::latest()->where('type', 'SMS')->orWhere('type', 'Sms for otp')->paginate(10, ['*'], 'sms');

        $emails = Message::latest()->where('type', 'Email')->paginate(10, ['*'], 'emails');
        $countries=Country::select('name','id','image', 'phone_code')->get();
        return view('admin.emails.messages',['messages'=> $messages,'users'=>$users, 'replaiers'=> $replaiers,'sms'=>$sms, 'emails'=> $emails,'countries'=> $countries]);
    }
    public function messageReplay(Request  $request,$id)
    {
        $request->validate([
            'desc'  =>  'nullable|string',
            'notes'  =>  'nullable|string',
        ]);
        $message=SupportCenter::find($id);
        if (!$message) {
            session()->flash('errors', __('site.support_message_not_found'));
            return redirect()->route('admin.messages.customer_messages');
        }
           $message->update([
                'desc'=>$request->desc,
                'notes'=>$request->notes,
                'replay'=>1,
                'replay_by'=>auth()->user()->id
           ]);

        session()->flash('success', __('site.edited_success'));
        return redirect()->route('admin.messages.customer_messages');
    }
    public function updateToken(Request $request)
    {
        try {
            auth()->user()->update(['fcm_token' => $request->token]);
            return response()->json([
                'success' => true
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false
            ], 500);
        }
    }
    public function storeMsg(Request $request)
    {
        try {
            $message = $this->messageService->postMessage($request->sender_id, $request->receiver_id);
            $response = $this->sendNotification($request->device_token, $this->messageService->composeNotificationContent($message));
            return ['message' => $message, 'response' => $response];
        } catch (\Exception $e) {
            report($e);
        }
    }
    public function sendNotification($deviceToken, $message)
    {
        $client = new ClientUrl();
        $data = [
            "to" => $deviceToken,
            "notification" => [
                "title" => 'Message Notification',
                "body" => $message,
            ]
        ];
        $headers = [
            'Authorization: key=' . env('FCM_SERVER_KEY'),
            'Content-Type: application/json',
        ];
        $rs = $client->url('https://fcm.googleapis.com/fcm/send')
        ->header($headers)
            ->returnTransfer(true)
            ->postFields(json_encode($data))
            ->exec()
            ->info();
        logger($rs->getInfo());
        return $rs;
    }

}
