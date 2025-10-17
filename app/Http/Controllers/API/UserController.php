<?php

namespace App\Http\Controllers\API;

use App\Models\Car;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderAccountant;
use App\Models\Offer;
use App\Models\Evaluate;
use App\Models\BankInfo;
use App\Models\UserData;
use App\Notifications\LocalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Validation\Rule;
use Pnlinh\InfobipSms\Facades\InfobipSms;
 use Illuminate\Notifications\Notifiable;
 use Carbon\Carbon;

use Exception;

class UserController extends BaseController
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function registerDriverInfo(Request $request, $id)
{
    $user = User::find($id);
    if (!$user || $user->type !== 'driver') {
        return $this->sendError('Driver not found.', ['Invalid driver ID']);
    }

    $validator = Validator::make($request->all(), [
        'location' => 'required|string',
        'national_id' => 'required|string|max:255',
        'track_type' => 'required|integer|exists:cars,id',
        'driving_license_number' => 'required|string|max:255',
        'track_license_number' => 'required|string|max:255',
        'track_number' => 'required|string|max:255',
        'company_id' => 'nullable|integer|exists:users,id',
        'longitude' => 'required|string',
        'latitude' => 'required|string'
    ]);

    if ($validator->fails()) {
        return $this->sendError('Validation Error.', $validator->errors()->all());
    }

    $userData = UserData::where('user_id', $user->id)->first();

    if (!$userData) {
        $userData = new UserData();
        $userData->user_id = $user->id;
    }

    $userData->fill([
        'location' => $request->location,
        'national_id' => $request->national_id,
        'track_type' => $request->track_type,
        'driving_license_number' => $request->driving_license_number,
        'track_license_number' => $request->track_license_number,
        'track_number' => $request->track_number,
        'company_id' => $request->company_id,
        'longitude' => $request->longitude,
        'latitude' => $request->latitude,
        'type' => 'driver'
    ]);

    $userData->save();

    return $this->sendResponse(['user_data' => $userData], 'Driver information updated successfully.');
}

    public function getNotifications($id)
    {
        $user = User::find($id);
        $notifications = auth()->user()->notifications()->select('id', 'data')->latest()->orderBy('created_at', 'ASC')->get();

        // Choose locale: request param ?lang=ar|en > app locale > en
        $locale = request('lang', app()->getLocale() ?: 'en');

        $items = [];
        foreach ($notifications as $noti) {
            $data = (array) $noti->data;

            // Resolve title (supports array ['ar'=>..,'en'=>..] or key string)
            $titleText = '';
            $titleAr = '';
            $titleEn = '';
            if (isset($data['title'])) {
                if (is_array($data['title'])) {
                    $titleAr = $data['title']['ar'] ?? ($data['title']['en'] ?? reset($data['title']));
                    $titleEn = $data['title']['en'] ?? ($data['title']['ar'] ?? reset($data['title']));
                    $titleText = $data['title'][$locale] ?? ($data['title']['en'] ?? $titleEn);
                } else {
                    $titleKey = (string) $data['title'];
                    $key = $titleKey && substr($titleKey, 0, 5) !== 'site.' ? 'site.' . $titleKey : $titleKey;
                    $titleAr = Lang::get($key, [], 'ar');
                    $titleEn = Lang::get($key, [], 'en');
                    $titleText = Lang::get($key);
                }
            }

            // Resolve body (supports array or key string)
            $bodyText = '';
            $bodyAr = '';
            $bodyEn = '';
            if (isset($data['body'])) {
                if (is_array($data['body'])) {
                    $bodyAr = $data['body']['ar'] ?? ($data['body']['en'] ?? reset($data['body']));
                    $bodyEn = $data['body']['en'] ?? ($data['body']['ar'] ?? reset($data['body']));
                    $bodyText = $data['body'][$locale] ?? ($data['body']['en'] ?? $bodyEn);
                } else {
                    $bodyKey = (string) $data['body'];
                    $key = $bodyKey && substr($bodyKey, 0, 5) !== 'site.' ? 'site.' . $bodyKey : $bodyKey;
                    $bodyAr = Lang::get($key, [], 'ar');
                    $bodyEn = Lang::get($key, [], 'en');
                    $bodyText = Lang::get($key);
                }
            }

            // Resolve target label when present
            $targetKey = $data['target'] ?? '';
            $targetText = $targetKey ? Lang::get(substr($targetKey, 0, 5) !== 'site.' ? 'site.' . $targetKey : $targetKey) : '';
            $targetTextAr = $targetKey ? Lang::get(substr($targetKey, 0, 5) !== 'site.' ? 'site.' . $targetKey : $targetKey, [], 'ar') : '';
            $targetTextEn = $targetKey ? Lang::get(substr($targetKey, 0, 5) !== 'site.' ? 'site.' . $targetKey : $targetKey, [], 'en') : '';

            // Build message: prefer body; else use title + target
            $message = trim(($bodyText ?: trim($titleText . ' ' . $targetText)) . ' ' . ($data['target_id'] ?? ''));

            // Build i18n message variants
            $messageAr = trim((($bodyAr ?: trim($titleAr . ' ' . $targetTextAr)) . ' ' . ($data['target_id'] ?? '')));
            $messageEn = trim((($bodyEn ?: trim($titleEn . ' ' . $targetTextEn)) . ' ' . ($data['target_id'] ?? '')));

            $items[] = [
                'id'        => $noti->id,
                'title'     => $titleText,
                'title_i18n'=> ['ar' => $titleAr, 'en' => $titleEn],
                'message'   => $message,
                'message_i18n' => ['ar' => $messageAr, 'en' => $messageEn],
                'body_i18n' => ['ar' => $bodyAr, 'en' => $bodyEn],
                'by'        => $data['user'] ?? '',
                'link'      => $data['link'] ?? '',
                'object'    => $data['object'] ?? null,
                'target_id' => $data['target_id'] ?? null,
                'target'    => $targetKey,
            ];
        }

        $success = [
            'count' => $notifications->count(),
            'notifications' => $items,
        ];
        return $this->sendResponse($success, 'User Notification.');
    }
     public function terms(Request $request){

         $success = [];
         if($request->type=='user'){
             if($request->lang=='en'){
                   $success['terms'] = Setting('customers_terms_conditions');
             }else{
                 $success['terms'] = Setting('customers_terms_conditions_ar');
             }

         }elseif($request->type=='driver'){
                    if($request->lang=='en'){
            $success['terms'] = Setting('drivers_terms_conditions');

                    }
            else{
                 $success['terms'] = Setting('drivers_terms_conditions_ar');

            }
            }

         elseif($request->type=='driverCompany'){
                    if($request->lang=='en'){
            $success['terms'] = Setting('shipping_company_terms_conditions');}else{
                  $success['terms'] = Setting('shipping_company_terms_conditions_ar');
            }
         }elseif($request->type=='factory'){
                    if($request->lang=='en'){
            $success['terms'] = Setting('factories_terms_conditions');

                    }
            else{
                   $success['terms'] = Setting('factories_terms_conditions_ar');
            }
         }else{
                    if($request->lang=='en'){
              $success['terms'] = Setting('factories_terms_conditions');}else{
                   $success['terms'] = Setting('factories_terms_conditions_ar');
              }
         }
         if(empty($success))
                 return $this->sendResponse($success, 'Type is missing..');
        return $this->sendResponse($success, 'users terms.');
     }

     public function policy(Request $request){
if($request->lang=='en'){
      $success['policy'] = Setting('policy');


}else{
      $success['policy'] = Setting('policy_ar');


}

        return $this->sendResponse($success, 'users policy.');
     }
    public function showUsers()
    {
        $users = User::with('userData')->where('type', 'user')->get();

        $success['users'] =  $users;
        return $this->sendResponse($success, 'Customers information.');
    }
    public function show($id)
    {
        $user = User::with(['userData'])->find($id);
        if (!$user) {
			$msgs=['Customer not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
        $pendOrder=Order::where('status','pend')->where('user_id',$user->id)->count();
        $approveOrder=Order::where('status','approve')->where('user_id',$user->id)->count();
        $pick_upOrder=Order::where('status','pick_up')->where('user_id',$user->id)->count();
        $deliveredOrder=Order::where('status','delivered')->where('user_id',$user->id)->count();
        $completeOrder=Order::where('status','complete')->where('user_id',$user->id)->count();
        $cancelOrder=Order::where('status','cancel')->where('user_id',$user->id)->count();
        $evaluates=Evaluate::where('user2_id',$user->id)->count();
        $avg= Evaluate::where('user2_id',$user->id)->avg('rate');
         $count_comment= Evaluate::where('user2_id',$user->id)->count('comment');;
   if (!empty($user->bank)) {
            $userData['bank'] = [
                'bank_name'                     =>      $user->bank->bank_name,
                'branch_name'                   =>      $user->bank->branch_name,
                'account_holder_name'           =>      $user->bank->account_holder_name,
                'account_number'                =>      $user->bank->account_number,
                'soft_code'                     =>      $user->bank->soft_code,
                'iban'                          =>      $user->bank->iban,
            ];
        }
        $success['pending_orders'] =  $pendOrder;
        $success['approval_orders'] =  $approveOrder;
        $success['pick_up_orders'] =  $pick_upOrder;
        $success['delivered_orders'] =  $deliveredOrder;
        $success['completed_orders'] =  $completeOrder;
        $success['canceled_orders'] =  $cancelOrder;
        $success['evaluates'] = $evaluates??0;
        $success['avg_evaluates'] = $avg??0;
          $success['avg_comment'] =$count_comment??0;
        $success['user'] =  $user;
        return $this->sendResponse($success, 'User information.');
    }
public function showFactory($id)
{
    $user = User::with(['userData', 'bank'])->find($id);
    if (!$user) {
        return $this->sendError('Data not found.', ['Customer Company not exists']);
    }

    $userData = [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'phone' => $user->phone,
        'type' => $user->type,
        'user_type' => $user->user_type,
        'phone_verified_at' => $user->phone_verified_at,
        'image' => $user->userData->image ?? null,
        'fcm_token' => $user->fcm_token,
        'active' => $user->active,
    ];

    if ($user->userData) {
        $userData += [
            'commercial_record' => $user->userData->commercial_record,
            'commercial_record_iamge_f' => $user->userData->commercial_record_iamge_f,
            'commercial_record_image_b' => $user->userData->commercial_record_image_b,
            'tax_card' => $user->userData->tax_card,
            'tax_card_image_f' => $user->userData->tax_card_image_f,
            'tax_card_image_b' => $user->userData->tax_card_image_b,
            'location' => $user->userData->location,
            'longitude' => $user->userData->longitude,
            'latitude' => $user->userData->latitude,
        ];
    }

    if (!empty($user->bank)) {
        $userData['bank'] = [
            'bank_name' => $user->bank->bank_name,
            'branch_name' => $user->bank->branch_name,
            'account_holder_name' => $user->bank->account_holder_name,
            'account_number' => $user->bank->account_number,
            'soft_code' => $user->bank->soft_code,
            'iban' => $user->bank->iban,
        ];
    }

    $statuses = ['pend', 'approve', 'pick_up', 'delivered', 'complete', 'cancel'];
    foreach ($statuses as $status) {
        $success[$status . '_orders'] = Order::where('status', $status)->where('user_id', $user->id)->count();
    }

    $success['user'] = $userData;

    return $this->sendResponse($success, 'Factory information.');
}

    public function showFactories()
    {
        $users = User::with(['userData', 'bank'])->where('type', 'factory')->get();
        $factoris[] = null;
        /* foreach($users as $user){
        $userData1 = [
            'name' => $user->name,
            'phone' => $user->phone,
            'user_type' => $user->user_type,
        ];
        foreach($user->userData as $uData){
             $userData1['commercial_record'] = $uData->commercial_record;
             $userData1['tax_card'] = $uData->tax_card;
             $userData1['location'] = $uData->location;
        }
            array_push($factoris,$userData);
        } */
        $success['count'] =  $users->count();
        $success['factoris'] =  $users;
        return $this->sendResponse($success, 'Factories information.');
    }
    public function showDrivers()
    {
        $users = User::with('userData')->where('type', 'driver')->get();
        $drivers[] = null;
        /* foreach($users as $user){
        $userData1 = [
            'name' => $user->name,
            'phone' => $user->phone,
            'user_type' => $user->user_type,
        ];
        foreach($user->userData as $uData){
             $userData1['commercial_record'] = $uData->commercial_record;
             $userData1['tax_card'] = $uData->tax_card;
             $userData1['location'] = $uData->location;
        }
            array_push($factoris,$userData);
        } */
        $success['count'] =  $users->count();
        $success['drivers'] =  $users;
        return $this->sendResponse($success, 'Drivers information.');
    }
    public function showCompanyDrivers(Request $request,$id)
    {
        $request['id']=$id;
        $comp=User::where('type','driverCompany')->find($id);

        if (!$comp) {
			$msgs= ['Shipping Company not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
        $users = User::whereHas('userData',function($q) use ($request) {
            return $q->when($request->status, function ($query) use ($request) {
            return $query->where('status',  $request->status);
        })->where('company_id',$request->id);
        })->where('type', 'driver')->get();

        $users1=[];
        $count=0;
        foreach($users as $user){
       // if( $user->userData->status==$request->status){
        $evaluates=Evaluate::where('user2_id',$user->id)->count();
        $avg= Evaluate::where('user2_id',$user->id)->avg('rate');
            $user1=[
                'id'=>$user->id,
                'name'=>$user->name,
                'email'=>$user->email,
                'phone'=>$user->phone,
                'phone_verified_at'=>$user->phone_verified_at,
                'type'=>$user->type,
                'fcm_token'=>$user->fcm_token,
                'active'=>$user->active,
                'image'=>$user->userData->image,
                'location'=>$user->userData->location,
                'latitude'=>$user->userData->latitude,
                'longitude'=>$user->userData->longitude,
                'national_id'=>$user->userData->national_id,
                'status'=>$user->userData->status,
                'vip'=>$user->userData->vip,
                'car_id'=>$user->userData->car->id,
                'car_name_ar'=>$user->userData->car->name_ar,
                'car_name_en'=>$user->userData->car->name_en,
                'car_image'=>$user->userData->car->image,
                'car_frames'=>$user->userData->car->frames,
                'car_weight'=>$user->userData->car->weight,
                'evaluate_count'=>$evaluates,
                'evaluate_average'=>$avg,
                'currency'=>Setting('currency_atr')
            ];
        //$user['car']=$car;
        $count++;
            array_push($users1,$user1);

        }
      //  $drivers[] = null;
        $success['count'] = $count;
        $success['drivers'] =  $users1;
        return $this->sendResponse($success, 'Company Drivers information.');
    }
    public function showDriverCompanies()
    {
        $users = User::with('userData')->where('type', 'driverCompany')->get();
        $drivers[] = null;
        /* foreach($users as $user){
        $userData1 = [
            'name' => $user->name,
            'phone' => $user->phone,
            'user_type' => $user->user_type,
        ];
        foreach($user->userData as $uData){
             $userData1['commercial_record'] = $uData->commercial_record;
             $userData1['tax_card'] = $uData->tax_card;
             $userData1['location'] = $uData->location;
        }
            array_push($factoris,$userData);
        } */
        $success['count'] =  $users->count();
        $success['driver_companies'] =  $users;
        return $this->sendResponse($success, 'Driver companies information.');
    }
    public function showDriver(Request $request,$id)
    {
        $user = User::with('userData')->find($id);
        if (!$user) {
			$msgs=['Driver not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
    //    $orders=null;

       $orders= Order::where('status','complete')->where('service_provider',$user->id)->get();
      if($request->has('month') && !empty($request->month) && $request->year && !empty($request->year)){
           $orders = DB::table('orders')//->join('order_accountants','order_accountants.order_id','=','orders.id')
      ->where('orders.service_provider',$user->id)
      ->where('orders.status','complete')
      ->whereMonth('created_at', '=', $request->month)
      ->whereYear('created_at', '=', $request->year)
      ->get();
      }

        $distance=0;
        $income_money=0;
        foreach($orders as $order){
        $statuses=OrderStatus::where('order_id',$order->id)->get();
      if($request->has('month') && !empty($request->month) && $request->year && !empty($request->year)){
        $statuses = DB::table('order_statuses')
      ->where('order_statuses.order_id',$order->id)
      ->whereMonth('created_at', '=', $request->month)
      ->whereYear('created_at', '=', $request->year)
      ->get();}
            $accountant=OrderAccountant::where('order_id',$order->id)->get()->first();
            foreach($statuses as $status){
                $distance = $distance + $status->distance;
            }
            $income_money=$income_money+( $accountant->service_provider_amount * 5) /100;
        }

        $cars = Car::where('id',$user->userData->track_type)->get()->first(); // add by mohammed-> please please check if it return Null Dev Abdo
        $track_en =   $cars->name_en??""; // add by mohammed -> please please check if it return Null Dev Abdo
        $track_ar =   $cars->name_ar??"" ; // add by mohammed-> please please check if it return Null Dev Abdo
   // 🆕 Add completed orders count
        $completedOrders = \App\Models\Order::where('service_provider', $user->id)
            ->where('status', 'completed')
            ->count();

       $distanceTraveled = 0;

foreach ($orders as $order) {
    $completedStatus = $order->statuses
        ->where('status', 'completed')
        ->first();

    if ($completedStatus && isset($completedStatus->distance)) {
        $distanceTraveled += $completedStatus->distance;
    }
}
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email'=>$user->email,
            'phone' => $user->phone,
            'type' => $user->type,
            'user_type' => $user->user_type,
            'image' => $user->userData->image,
            'national_id' => $user->userData->national_id,
            'national_id_image_f' => $user->userData->national_id_image_f,
            'national_id_image_b' => $user->userData->national_id_image_b,
            'track_type'                =>  $user->userData->track_type,
            'track_en'                =>   $track_en, // add by mohammed
            'track_ar'                =>   $track_ar, // add by mohammed
            'driving_license_number' => $user->userData->driving_license_number,
            'driving_license_image_f' => $user->userData->driving_license_image_f,
            'driving_license_image_b' => $user->userData->driving_license_image_b,
            'track_license_number' => $user->userData->track_license_number,
            'track_license_image_f' => $user->userData->track_license_image_f,
            'track_license_image_b' => $user->userData->track_license_image_b,
            'track_image_f'         => $user->userData->track_image_f,
            'track_image_b'         => $user->userData->track_image_b,
            'track_image_s'         => $user->userData->track_image_s,
            'track_number'              =>  $user->userData->track_number,
            'company_id'                =>  $user->userData->company_id,
            'location'                  =>  $user->userData->location,
            'latitude'                  =>  $user->userData->latitude,
            'longitude' => $user->userData->longitude,
            'works_hours'   =>$user->userData->works_hours,
            'distance_traveled'    =>  $distance,
            'income_money'      =>  number_format($income_money,2),
            'available_balance' =>  number_format($user->userData->balance,2),
            'pending_balance' =>  number_format($user->userData->pending_balance,2),
            'total_balance' =>  number_format($user->userData->balance + $user->userData->pending_balance,2),

            'status' => $user->userData->status,
            'phone_verified_at'=>$user->phone_verified_at,
            'active'=>$user->active,
               'completed_orders' => $completedOrders,
            'distance_traveled' => $distanceTraveled,
        ];
        $completeOrder=$orders->count();
        $offersCount=Offer::where('driver_id',$id)->get()->count();
      if($request->has('month') && !empty($request->month) && $request->year && !empty($request->year)){
        $offersCount = DB::table('offers')
        ->where('offers.driver_id',$user->id)
          ->whereMonth('created_at', '=', $request->month)
          ->whereYear('created_at', '=', $request->year)
        ->count();
      }
        $success['completed_orders'] =  $completeOrder;
        $success['offersCount'] =  $offersCount;
        $success['user'] =  $userData;
        return $this->sendResponse($success, 'Driver information.');
    }
   public function showDriverCompany($id, Request $request)
{
    $user = User::with(['userData', 'bank'])->find($id);
    if (!$user) {
        $msgs = ['Shipping Company not exists'];
        return $this->sendError('Data not found.', $msgs);
    }

    $drivers = [];
    $ddrs = UserData::with(['user'])->where('company_id', $id)
        ->when($request->status, function ($query) use ($request) {
            return $query->where('status', $request->status);
        })->get();

    foreach ($ddrs as $muser) {
        $evaluates = Evaluate::where('user2_id', $muser->id)->count();
        $avg = Evaluate::where('user2_id', $muser->id)->avg('rate');
        $wuser = User::find($muser->user_id);
        $wuser_data = UserData::find($muser->user_id);

        // Get all completed order IDs for this driver
        $completedOrderIds = \App\Models\Order::where('service_provider', $muser->user_id)
            ->where('status', 'completed')
            ->pluck('id');

        // Count completed orders
        $completedOrders = $completedOrderIds->count();

        // Sum distances from order_statuses
        $distanceTraveled = \App\Models\OrderStatus::whereIn('order_id', $completedOrderIds)
            ->where('status', 'completed')
            ->sum('distance');

        $dd = [
            'id' => $muser->user_id,
            'name' => $wuser->name,
            'email' => $wuser->email,
            'fcm_token' => $muser->fcm_token,
            'phone' => $wuser->phone,
            'image' => $muser->image,
            'national_id' => $muser->national_id,
            'national_id_image_f' => $muser->national_id_image_f,
            'national_id_image_b' => $muser->national_id_image_b,
            'track_type' => $muser->track_type,
            'driving_license_number' => $muser->driving_license_number,
            'driving_license_image_f' => $muser->driving_license_image_f,
            'driving_license_image_b' => $muser->driving_license_image_b,
            'track_license_number' => $muser->track_license_number,
            'track_license_image_f' => $muser->track_license_image_f,
            'track_license_image_b' => $muser->track_license_image_b,
            'track_image_f' => $muser->track_image_f,
            'track_image_b' => $muser->track_image_b,
            'track_image_s' => $muser->track_image_s,
            'track_number' => $muser->track_number,
            'company_id' => $muser->company_id,
            'location' => $muser->location,
            'longitude' => $muser->longitude,
            'latitude' => $muser->latitude,
            'status' => $muser->status,
            'evaluates' => $evaluates,
            'avg' => $avg ?: 0,
            'user_data' => $wuser_data,
            'completed_orders' => $completedOrders,
            'distance_traveled' => $distanceTraveled,
        ];

        array_push($drivers, $dd);
    }

    $drivers_count = UserData::where('company_id', $id)->count();
    $userData = [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'phone' => $user->phone,
        'type' => $user->type,
        'user_type' => $user->user_type,
        'image' => optional($user->userData)->image,
        'commercial_record' => optional($user->userData)->commercial_record,
        'national_id_image_f' => optional($user->userData)->national_id_image_f,
        'national_id_image_b' => optional($user->userData)->national_id_image_b,
        'tax_card' => optional($user->userData)->tax_card,
        'tax_card_image_f' => optional($user->userData)->tax_card_image_f,
        'tax_card_image_b' => optional($user->userData)->tax_card_image_b,
        'commercial_record_image_f' => optional($user->userData)->commercial_record_image_f,
        'commercial_record_image_b' => optional($user->userData)->commercial_record_image_b,
        'status' => optional($user->userData)->status,
        'phone_verified_at' => $user->phone_verified_at,
        'active' => $user->active,
        'drivers_count' => $drivers_count,
        'drivers' => $drivers,
    ];

    if (!empty($user->bank)) {
        $userData['bank'] = [
            'bank_name' => $user->bank->bank_name,
            'branch_name' => $user->bank->branch_name,
            'account_holder_name' => $user->bank->account_holder_name,
            'account_number' => $user->bank->account_number,
            'soft_code' => $user->bank->soft_code,
            'iban' => $user->bank->iban,
        ];
    }

    $success['user'] = $userData;
    return $this->sendResponse($success, 'Driver company information.');
}


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function updateUserFCMToken(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
			$msgs= ['User not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'string|required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $msgs = [];
            foreach ($errors->all() as  $ind => $message) {
                array_push($msgs, $message);
            }
            return $this->sendError('Validation Error.', $msgs);
        }
        $input = $request->all();
        $userData = UserData::where('user_id', $user->id)->get()->first();
        DB::beginTransaction();
        $user->update([
            'fcm_token' => $input['fcm_token'],
        ]);

        DB::commit();
        $success['user'] =  [
            'id' => $user->id,
            'name' => $user->name,
            'fcm_token'=>$user->fcm_token,
            'phone' => $user->phone,
            'image' => $userData->image,
        ];

        return $this->sendResponse($success, 'FCM token for User updated successfully.');
    }
   public function updateUser(Request $request, $id)
{
    $user = User::find($id);
    if (!$user) {
        return $this->sendError('Data not found.', ['Customer not exists']);
    }

    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
        'phone' => ['required', 'string', 'max:30', Rule::unique('users')->ignore($user->id)],
        'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10240',
    ]);

    if ($validator->fails()) {
        return $this->sendError('Validation Error.', $validator->errors()->all());
    }

    $userData = UserData::where('user_id', $user->id)->first();

    if ($request->hasFile('image')) {
        if ($userData && $userData->image !== 'uploads/users/default.png' && file_exists(public_path($userData->image))) {
            unlink(public_path($userData->image));
        }

        $imagePath = 'uploads/users/' . $request->image->hashName();
        Image::make($request->image)->save(public_path($imagePath));

        if ($userData) {
            $userData->update(['image' => $imagePath]);
        } else {
            UserData::create([
                'user_id' => $user->id,
                'image' => $imagePath,
                'phone' => $user->phone,
                'type' => $user->type
            ]);
        }
    }

    DB::beginTransaction();

    $updateData = [
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
    ];

    if ($request->filled('password')) {
        $updateData['password'] = bcrypt($request->password);
    }

    $user->update($updateData);

    $data = [
        'title' => 'edit',
        'body' => 'edit_body',
        'target' => 'customer',
        'link'  => route('admin.customers.index', ['name' => $user->name]),
        'target_id' => $user->name,
        'sender' => $user->name,
    ];

    $admins = User::where('user_type', 'manage')->get();
    foreach ($admins as $admin) {
        Notification::send($admin, new LocalNotification($data));
    }

    DB::commit();

    return $this->sendResponse([
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'fcm_token' => $user->fcm_token,
            'phone' => $user->phone,
            'image' => $userData->image ?? null,
        ]
    ], 'User updated successfully.');
}
public function updateFactory(Request $request, $id)
{
    $user = User::find($id);
    if (!$user) {
        $msgs = ['Customer Company not exists'];
        return $this->sendError('Data not found.', $msgs);
    }

    $validator = Validator::make($request->all(), [
        'name' => 'string|max:255|required',
        'email' => ['nullable', Rule::unique('users')->ignore($user->id)],
        'phone' => ['required', Rule::unique('users')->ignore($user->id)],
        'commercial_record' => ['required', Rule::unique('user_data', 'user_id')->ignore($user->id)],
        'tax_card' => ['required', Rule::unique('user_data', 'user_id')->ignore($user->id)],
        'location' => 'nullable',
        'longitude' => 'nullable',
        'latitude' => 'nullable',
        'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10240',
        'commercial_record_image_f' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10240',
        'commercial_record_image_b' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10240',
        'tax_card_image_f' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10240',
        'tax_card_image_b' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10240',
        'bank_name' => 'nullable|string',
        'branch_name' => 'nullable|string',
        'account_holder_name' => 'nullable|string',
        'account_number' => 'nullable|string',
        'soft_code' => 'nullable|string',
        'iban' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return $this->sendError('Validation Error.', $validator->errors()->all());
    }

    $userData = UserData::where('user_id', $user->id)->first();

    if ($request->hasFile('image')) {
        if (!empty($userData->image) && file_exists(public_path($userData->image))) {
            unlink(public_path($userData->image));
        }
        $imagePath = 'uploads/users/' . $request->image->hashName();
        Image::make($request->image)->save(public_path($imagePath));
        $userData->image = $imagePath;
    }

    foreach (['commercial_record_image_f', 'commercial_record_image_b', 'tax_card_image_f', 'tax_card_image_b'] as $imgField) {
        if ($request->hasFile($imgField)) {
            $path = 'uploads/' . ($imgField === 'tax_card_image_f' || $imgField === 'tax_card_image_b' ? 'tax_cards' : 'commercial_records');
            $imagePath = $path . '/' . $request->$imgField->hashName();
            Image::make($request->$imgField)->save(public_path($imagePath));
            $userData->$imgField = $imagePath;
        }
    }

    DB::beginTransaction();
    try {
        $user->update([
            'name' => $request->name,
            'email' => $request->email ?? "",
            'phone' => $request->phone,
            'password' => $request->filled('password') ? bcrypt($request->password) : $user->password,
        ]);

        if ($userData) {
            $userData->update([
                'commercial_record' => $request->commercial_record,
                'tax_card' => $request->tax_card,
                'location' => $request->location ?? "",
                'longitude' => $request->longitude ?? "0.0",
                'latitude' => $request->latitude ?? "0.0",
                'image' => $userData->image ?? $userData->image,
                'commercial_record_image_f' => $userData->commercial_record_image_f ?? null,
                'commercial_record_image_b' => $userData->commercial_record_image_b ?? null,
                'tax_card_image_f' => $userData->tax_card_image_f ?? null,
                'tax_card_image_b' => $userData->tax_card_image_b ?? null,
            ]);
        }

        $userBank = BankInfo::where('user_id', $user->id)->first();
        if ($request->filled(['bank_name', 'branch_name', 'account_holder_name', 'account_number'])) {
            $bankData = [
                'user_id' => $user->id,
                'bank_name' => $request->bank_name,
                'branch_name' => $request->branch_name,
                'account_holder_name' => $request->account_holder_name,
                'account_number' => $request->account_number,
                'soft_code' => $request->soft_code,
                'iban' => $request->iban,
            ];
            $userBank ? $userBank->update($bankData) : $userBank = BankInfo::create($bankData);
        }

        $data = [
            'title' => 'edit',
            'body' => 'edit_body',
            'target' => 'factory',
            'link' => route('admin.factories.index', ['name' => $user->name]),
            'target_id' => $user->name,
            'sender' => $user->name,
        ];
        $admins = User::where('user_type', 'manage')->get();
        Notification::send($admins, new LocalNotification($data));

        DB::commit();

        $success['user'] = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'fcm_token' => $user->fcm_token,
            'phone' => $user->phone,
            'image' => $userData->image ?? null,
            'commercial_record' => $userData->commercial_record ?? null,
            'commercial_record_image_f' => $userData->commercial_record_image_f ?? null,
            'commercial_record_image_b' => $userData->commercial_record_image_b ?? null,
            'tax_card' => $userData->tax_card ?? null,
            'tax_card_image_f' => $userData->tax_card_image_f ?? null,
            'tax_card_image_b' => $userData->tax_card_image_b ?? null,
            'location' => $userData->location ?? null,
            'latitude' => $userData->latitude ?? null,
            'longitude' => $userData->longitude ?? null,
            'bank' => $userBank ?? null,
        ];

        return $this->sendResponse($success, 'Factory updated successfully.');
    } catch (\Exception $e) {
        DB::rollback();
        return $this->sendError('Update Failed', [$e->getMessage()]);
    }
}


    public function updateDriver(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
			$msgs=['Driver not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|required',
            'email'=>'required|email',
            'email' => ['nullable', Rule::unique('users')->ignore($user->id),],
            'phone' => 'required|string|max:30|unique:users,phone',
            'phone' => ['required', Rule::unique('users')->ignore($user->id),],
            'national_id' => 'required|string|max:255|unique:user_data,national_id',
            'national_id' => ['required', Rule::unique('user_data', 'user_id')->ignore($user->id),],
            'image' => 'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'national_id_image_f' => 'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'national_id_image_b' => 'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'track_type' => 'required|exists:cars,id',
            'driving_license_number' => 'required|string|max:255|unique:user_data,driving_license_number',
            'driving_license_number' => ['required', Rule::unique('user_data', 'user_id')->ignore($user->id),],
            'track_license_number' => 'required|string|max:255|unique:user_data,track_license_number',
            'track_license_number' => ['required', Rule::unique('user_data', 'user_id')->ignore($user->id),],
            'track_number' => 'required|string|max:255|unique:user_data,track_number',
            'track_number' => ['required', Rule::unique('user_data', 'user_id')->ignore($user->id),],
            'company_id' => 'nullable',
            'status' => 'nullable',
            'location'=>'nullable',
            'longitude'=>'nullable',
            'latitude'=>'nullable',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $msgs = [];
            foreach ($errors->all() as  $ind => $message) {
                array_push($msgs, $message);
            }
            return $this->sendError('Validation Error.', $msgs);
        }
        $userData = UserData::where('user_id', $user->id)->get()->first();
        if ($request->image) {
            if ($user->image != 'uploads/users/default.png') {
                if (is_file(public_path($user->image))) {
                    unlink(public_path($user->image));
                }
            }
            Image::make($request->image)
                ->save(public_path('uploads/users/' . $request->image->hashName()));
            $userData->update([
                'image' => 'uploads/users/' . $request->image->hashName()
            ]);
        }
        if ($request->national_id_image_f) {
            if ($user->national_id_image_f != 'uploads/users/default.png') {
                if (is_file(public_path($user->national_id_image_f))) {
                    unlink(public_path($user->national_id_image_f));
                }
            }
            Image::make($request->national_id_image_f)
                ->save(public_path('uploads/national_ids/' . $request->national_id_image_f->hashName()));
            $userData->update([
                'national_id_image_f' => 'uploads/national_ids/' . $request->national_id_image_f->hashName()
            ]);
        }
        if ($request->national_id_image_b) {
            if ($user->national_id_image_b != 'uploads/users/default.png') {
                if (is_file(public_path($user->national_id_image_b))) {
                    unlink(public_path($user->national_id_image_b));
                }
            }
            Image::make($request->national_id_image_b)
                ->save(public_path('uploads/national_ids/' . $request->national_id_image_b->hashName()));
            $userData->update([
                'national_id_image_b' => 'uploads/national_ids/' . $request->national_id_image_b->hashName()
            ]);
        }
        $input['driving_license_image_f'] = null;
        if ($request->file('driving_license_image_f')) {
            Image::make($request->driving_license_image_f)
                ->save(public_path('uploads/driving_licenses/' . $request->driving_license_image_f->hashName()));
                 $userData->update([
                'driving_license_image_f' => 'uploads/driving_licenses/' . $request->driving_license_image_f->hashName()
            ]);

        }

        if ($request->file('driving_license_image_b')) {
            Image::make($request->driving_license_image_b)
                ->save(public_path('uploads/driving_licenses/' . $request->driving_license_image_b->hashName()));
  $userData->update([
                'driving_license_image_b' => 'uploads/driving_licenses/' . $request->driving_license_image_b->hashName()
            ]);        }

        if ($request->file('track_license_image_f')) {
            Image::make($request->track_license_image_f)
                ->save(public_path('uploads/truck_licenses/' . $request->track_license_image_f->hashName()));

         $userData->update([
                'track_license_image_f' => 'uploads/truck_licenses/' . $request->track_license_image_f->hashName()
            ]);

        }

        if ($request->file('track_license_image_b')) {
            Image::make($request->track_license_image_b)
                ->save(public_path('uploads/truck_licenses/' . $request->track_license_image_b->hashName()));

        $userData->update([
                'track_license_image_b' => 'uploads/truck_licenses/' . $request->track_license_image_b->hashName()
            ]);
        }


        if ($request->file('track_image_f')) {
            Image::make($request->track_image_f)
                ->save(public_path('uploads/trucks/' . $request->track_image_f->hashName()));


      $userData->update([
                'track_image_f' => 'uploads/trucks/' . $request->track_image_f->hashName()
            ]);
            }


        if ($request->file('track_image_b')) {
            Image::make($request->track_image_b)
                ->save(public_path('uploads/trucks/' . $request->track_image_b->hashName()));

     $userData->update([
                'track_image_b' => 'uploads/trucks/' . $request->track_image_b->hashName()
            ]);
        }


        if ($request->file('track_image_s')) {
            Image::make($request->track_image_s)
                ->save(public_path('uploads/trucks/' . $request->track_image_s->hashName()));
        $userData->update([
                'track_image_s' => 'uploads/trucks/' . $request->track_image_s->hashName()
            ]);
        }
        $input = $request->all();
        DB::beginTransaction();
        $input['national_id_image'] = null;
   if(isset($input['password'])){
              $user->update([
            'name' => $input['name'],
            'email'=>$input['email'],
            'phone' =>  $input['phone'],
            'password' => bcrypt($input['password'])
            // 'password' => bcrypt($input['password']), // add by mohammed
            //update That to Isset Abdo
        ]);
        }else{
        $user->update([
            'name' => $input['name'],
            'email'=>$input['email'],
            'phone' =>  $input['phone'],
            // 'password' => bcrypt($input['password']), // add by mohammed
            //update That to Isset Abdo
        ]);

        }

            $userData->update([
            'national_id'               =>  $input['national_id'],
            'track_type'                =>  $input['track_type'],
            'driving_license_number'    =>  $input['driving_license_number'],
            'track_license_number'      =>  $input['track_license_number'],
            'track_number'              =>   $input['track_number'],
            'company_id'                =>  isset( $input['company_id'])?$input['company_id']:$userData->company_id,
            'location'                  => isset( $input['location'])? $input['location']:$userData->location,
            'longitude'              =>  isset( $input['longitude'])?$input['longitude']:$userData->longitude,
            'latitude'                      =>isset( $input['latitude'])?  $input['latitude']:$userData->latitude,
            'type' =>  'driver',
        ]);


        $data = [
            'title' => 'edit',
            'body' => 'edit_body',
            'target' => 'driver',
            'link'  => route('admin.drivers.index', ['name' => $user->name]),
            'target_id' => $user->name,
            'sender' => $user->name,
        ];
        $users = User::where('user_type', 'manage')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }
        DB::commit();
        $user = User::with('userData')->find($id);
        $success['user'] = [
            'id' => $user->id,
            'name'                      =>  $user->name,
            'email'                     =>  $user->email,
            'fcm_token'=>$user->fcm_token,
            'phone'                     =>  $user->phone,
            'image'                     =>  $user->userData->image,
            'national_id'               =>  $user->userData->national_id,
            'national_id_image_f'       =>  $user->userData->national_id_image_f,
            'national_id_image_b'       =>  $user->userData->national_id_image_b,
            'track_type'                =>  $user->userData->track_type,
            'driving_license_number' => $user->userData->driving_license_number,
            'driving_license_image_f' => $user->userData->driving_license_image_f,
            'driving_license_image_b' => $user->userData->driving_license_image_b,
            'track_license_number' => $user->userData->track_license_number,
            'track_license_image_f' => $user->userData->track_license_image_f,
            'track_license_image_b' => $user->userData->track_license_image_b,
            'track_image_f'         => $user->userData->track_image_f,
            'track_image_b'         => $user->userData->track_image_b,
            'track_image_s'         => $user->userData->track_image_s,
            'track_number'              =>  $user->userData->track_number,
            'company_id'                =>  $user->userData->company_id,
            'location'                  =>  $user->userData->location,
            'longitude'                      =>  $user->userData->longitude,
            'latitude'                      =>  $user->userData->latitude,
        ];

        return $this->sendResponse($success, 'Driver updated successfully.');
    }
    public function updateDriverCompany(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
			$msgs=['Driver Company not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|required',
            'email'=>'email',
            'email' => ['nullable', Rule::unique('users')->ignore($user->id),],
            'phone' => 'required|string|max:30|unique:users,phone',
            'phone' => ['required', Rule::unique('users')->ignore($user->id),],
            'commercial_record' => 'required|string|max:255|unique:user_data,commercial_record',
            'commercial_record' => ['required', Rule::unique('user_data', 'user_id')->ignore($user->id),],
            'tax_card' => 'required|string|max:255|unique:user_data,tax_card',
            'tax_card' => ['required', Rule::unique('user_data', 'user_id')->ignore($user->id),],
            'location'=>'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
            'latitude' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'commercial_record_image_f' => 'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'commercial_record_image_b' => 'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'tax_card_image_f' => 'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'tax_card_image_b' => 'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'bank_name'                 =>      'nullable|string',
            'branch_name'               =>      'nullable|string',
            'account_holder_name'       =>      'nullable|string',
            'account_number'            =>      'nullable|string',
            'soft_code'                 =>      'nullable|string',
            'iban'                      =>      'nullable|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $msgs = [];
            foreach ($errors->all() as  $ind => $message) {
                array_push($msgs, $message);
            }
            return $this->sendError('Validation Error.', $msgs);
        }

        $userData = UserData::where('user_id', $user->id)->get()->first();
        if ($request->image) {
            if ($user->image != 'uploads/users/default.png') {
                if (file_exists(public_path($user->image))) {
                    unlink(public_path($user->image));
                }
            }
            Image::make($request->image)
                ->save(public_path('uploads/users/' . $request->image->hashName()));
            $userData->update([
                'image' => 'uploads/users/' . $request->image->hashName()
            ]);
        }
        if ($request->commercial_record_image_f) {
            if ($user->commercial_record_image_f != 'uploads/users/default.png') {
                if (file_exists(public_path($user->commercial_record_image_f))) {
                    unlink(public_path($user->commercial_record_image_f));
                }
            }
            Image::make($request->commercial_record_image_f)
                ->save(public_path('uploads/commercial_records/' . $request->commercial_record_image_f->hashName()));
            $userData->update([
                'commercial_record_image_f' => 'uploads/commercial_records/' . $request->commercial_record_image_f->hashName()
            ]);
        }
        if ($request->commercial_record_image_b) {
            if ($user->commercial_record_image_b != 'uploads/users/default.png') {
                if (file_exists(public_path($user->commercial_record_image_b))) {
                    unlink(public_path($user->commercial_record_image_b));
                }
            }
            Image::make($request->commercial_record_image_b)
                ->save(public_path('uploads/commercial_records/' . $request->commercial_record_image_b->hashName()));
            $userData->update([
                'commercial_record_image_b' => 'uploads/commercial_records/' . $request->commercial_record_image_b->hashName()
            ]);
        }
        if ($request->tax_card_image_f) {
            if ($user->tax_card_image_f != 'uploads/users/default.png') {
                if (file_exists(public_path($user->tax_card_image_f))) {
                    unlink(public_path($user->tax_card_image_f));
                }
            }
            Image::make($request->tax_card_image_f)
                ->save(public_path('uploads/tax_cards/' . $request->tax_card_image_f->hashName()));
            $userData->update([
                'tax_card_image_f' =>  'uploads/tax_cards/' . $request->tax_card_image_f->hashName()
            ]);
        }
        if ($request->tax_card_image_b) {
            if ($user->tax_card_image_b != 'uploads/users/default.png') {
                if (file_exists(public_path($user->tax_card_image_b))) {
                    unlink(public_path($user->tax_card_image_b));
                }
            }
            Image::make($request->tax_card_image_b)
                ->save(public_path('uploads/tax_cards/' . $request->tax_card_image_b->hashName()));
            $userData->update([
                'tax_card_image_b' => 'uploads/tax_cards/' . $request->tax_card_image_b->hashName()
            ]);
        }
        $input = $request->all();
        DB::beginTransaction();
      if(isset($input['password'])){
              $user->update([
            'name' => $input['name'],
            'email'=>$input['email'],
            'phone' =>  $input['phone'],
            'password' => bcrypt($input['password'])
            // 'password' => bcrypt($input['password']), // add by mohammed
            //update That to Isset Abdo
        ]);
        }else{
        $user->update([
            'name' => $input['name'],
            'email'=>$input['email'],
            'phone' =>  $input['phone'],
            // 'password' => bcrypt($input['password']), // add by mohammed
            //update That to Isset Abdo
        ]);

        }

        $userData->update([
            'commercial_record'               =>  $input['commercial_record'],
            'tax_card'         =>  $input['tax_card'],
            'location'          =>  $input['location'],
        ]);
      if($request->has('longitude')){
           $userData->update([

            'longitude'                =>  $input['longitude'],
            'latitude'                =>  $input['latitude'],
        ]);}
        if ($request->has('bank_name') && $request->has('branch_name') && $request->has('account_holder_name') && $request->has('account_number')) {
            $userBank = BankInfo::where('user_id', $user->id)->first();
            if (!empty($userBank)) {
                $userBank->update([
                    'user_id'                   =>      $user->id,
                    'bank_name'                 =>      $input['bank_name'],
                    'branch_name'               =>      $input['branch_name'],
                    'account_holder_name'       =>      $input['account_holder_name'],
                    'account_number'            =>      $input['account_number'],
                    'soft_code'                 =>      $input['soft_code'],
                    'iban'                      =>      $input['iban'],
                ]);
            } else {
                $userBank = BankInfo::create([
                    'user_id'                   =>      $user->id,
                    'bank_name'                 =>      $input['bank_name'],
                    'branch_name'               =>      $input['branch_name'],
                    'account_holder_name'       =>      $input['account_holder_name'],
                    'account_number'            =>      $input['account_number'],
                    'soft_code'                 =>      $input['soft_code'],
                    'iban'                      =>      $input['iban'],
                ]);
            }
        }
        $data = [
            'title' => 'edit',
            'body' => 'edit_body',
            'target' => 'shipping_company',
            'link'  => route('admin.companies.index', ['name' => $user->name]),
            'target_id' => $user->name,
            'sender' => $user->name,
        ];
        $users = User::where('user_type', 'manage')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }
        DB::commit();
        $user = User::with(['userData', 'bank'])->find($id);
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email'=>$user->email,
            'fcm_token'=>$user->fcm_token,
            'phone' => $user->phone,
            'image' => $user->userData->image,
            'commercial_record' => $user->userData->commercial_record,
            'commercial_record_image_f' => $user->userData->commercial_record_image_f,
            'commercial_record_image_b' => $user->userData->commercial_record_image_b,
            'tax_card' => $user->userData->tax_card,
            'tax_card_image_f' => $user->userData->tax_card_image_f,
            'tax_card_image_b' => $user->userData->tax_card_image_b,
            'location'          =>  $user->userData->location,
            'longitude' => $user->userData->longitude,
            'latitude' => $user->userData->latitude,

        ];
        if (!empty($user->bank)) {
            $userData['bank'] = [
                'bank_name'                     =>      $user->bank->bank_name,
                'branch_name'                   =>      $user->bank->branch_name,
                'account_holder_name'           =>      $user->bank->account_holder_name,
                'account_number'                =>      $user->bank->account_number,
                'soft_code'                     =>      $user->bank->soft_code,
                'iban'                          =>      $user->bank->iban,
            ];
        }
        $success['user'] = $userData;
        return $this->sendResponse($success, 'Driver updated successfully.');
    }
    public function verifyPhone(Request $request, $id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                $msgs= ['User not exists'];
                return $this->sendError('Data not found.',$msgs);
            }
            // $request->validate(['code' => 'required|exists:reset_code_passwords,code']);
 $current_timestamp = Carbon::now();
            $user->update(['phone_verified_at' => $current_timestamp ]);
            $success['user'] = [
                'id' => $user->id,
                'name' => $user->name,
                'fcm_token'=>$user->fcm_token,
                'phone' => $user->phone,
                'phone_verified_at'=>$user->phone_verified_at
            ];
            return $this->sendResponse( $user, 'Phone number verified successfully.');
        }catch (Exception $e) {
            return $this->sendError('Validation Error.','OTP incorrect Please try again');
        }

    }
    public function setLocation(Request $request, $id)
    {
        $user = User::find($id);
        $userData = UserData::where('user_id', $user->id)->get()->first();
        if (!$user) {
			$msgs=['User not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
        $validator = Validator::make($request->all(), [
            'location'  =>  'required|string|max:255',
            'longitude' => 'required|string|max:255',
            'latitude' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            $msgs = [];
            foreach ($errors->all() as  $ind => $message) {
                array_push($msgs, $message);
            }
            return $this->sendError('Validation Error.', $msgs);
        }
        if ($userData) {
            $userData->update(['location'=>$request['location'],'longitude' => $request['longitude'], 'latitude' => $request['latitude']]);
        } else {
            UserData::create(['location'=>$request['location'],'longitude' => $request['longitude'], 'latitude' => $request['latitude'], 'user_id' => $user->id]);
        }
        $success['user'] = [
            'id' => $user->id,
            'name' => $user->name,
            'fcm_token'=>$user->fcm_token,
            'phone' => $user->phone,
            'longitude'=>$userData->longitude,
            'latitude'=>$userData->latitude
        ];
        return $this->sendResponse($success, 'Location updated successfully.');
    }
    public function getLocation($id)
    {
        $user = User::find($id);

        $longitude= UserData::where('user_id', $id)->value('longitude');
         $latitude= UserData::where('user_id', $id)->value('latitude');

        if (!$user) {
			$msgs=['User not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
        $success['user'] = [
            'id' => $user->id,
            'name' => $user->name,
            'email'=>$user->email,
            'fcm_token'=>$user->fcm_token,
            'phone' => $user->phone,
            'longitude'=>$longitude,
            'latitude'=>  $latitude
        ];
        return $this->sendResponse($success, 'User Location Informaion.');
    }
  public function setOnline($id)
{
    $user = User::find($id);
    if (! $user) {
        return $this->sendError('Data not found.', ['User not exists']);
    }

    // pull driver extra row once (only matters when user is a driver)
    $driverData = ($user->type === 'driver')
        ? UserData::where('user_id', $user->id)->first()
        : null;

    /* ---------- toggle online flag ---------- */
    if ($user->online == 1) {                                        // was online → go offline
        $user->update(['online' => 0]);

        // if driver & previously “available”, switch to “un_available”
        if ($driverData && $driverData->status === 'un_available') {
            $driverData->update(['status' => 'available']);
        }

        $msg = 'Change user to offline successfully.';
    } else {                                                         // was offline → go online
        $user->update(['online' => 1]);

        // if driver & previously “un_available”, switch to “available”
        if ($driverData && $driverData->status === 'available') {
            $driverData->update(['status' => 'un_available']);
        }

        $msg = 'Change user to online successfully.';
    }

    /* ---------- response ---------- */
    return $this->sendResponse([
        'user' => [
            'id'        => $user->id,
            'name'      => $user->name,
            'email'     => $user->email,
            'fcm_token' => $user->fcm_token,
            'phone'     => $user->phone,
            'online'    => $user->online,
            'driver_status' => $driverData->status ?? null
        ]
    ], $msg);
}

    public function getOnline( $id)
    {
        $user = User::find($id);
        if (!$user) {
			$msgs= ['User not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
        $success['user'] = [
            'id' => $user->id,
            'name' => $user->name,
            'email'=>$user->email,
            'fcm_token'=>$user->fcm_token,
            'phone' => $user->phone,
            'online'=>$user->online
        ];
        return $this->sendResponse($success, 'User Online Informaion.');
    }
    public function driverStatus(Request $request, $id)
    {
        $user = User::where('type', 'driver')->find($id);
        $userData = UserData::where('user_id', $user->id)->where('type', 'driver')->get()->first();
        if (!$user) {
			$msgs= ['Driver not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|max:30',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            $msgs = [];
            foreach ($errors->all() as  $ind => $message) {
                array_push($msgs, $message);
            }
            return $this->sendError('Validation Error.', $msgs);
        }
        $userData->update(['status' => $request->status]);
        $success['user'] = [
            'id' => $user->id,
            'name' => $user->name,
            'email'=>$user->email,
            'phone' => $user->phone,
            'type' => $user->type,
            'national_id'               =>  $user->userData->national_id,
            'national_id_image_f'       =>  $user->userData->national_id_image_f,
            'national_id_image_b'       =>  $user->userData->national_id_image_b,
            'track_type'                =>  $user->userData->track_type,
            'driving_license_number' => $user->userData->driving_license_number,
            'driving_license_image_f' => $user->userData->driving_license_image_f,
            'driving_license_image_b' => $user->userData->driving_license_image_b,
            'track_license_number' => $user->userData->track_license_number,
            'track_license_image_f' => $user->userData->track_license_image_f,
            'track_license_image_b' => $user->userData->track_license_image_b,
            'track_image_f'         => $user->userData->track_image_f,
            'track_image_b'         => $user->userData->track_image_b,
            'track_image_s'         => $user->userData->track_image_s,
            'track_number'              =>  $user->userData->track_number,
            'company_id'                =>  $user->userData->company_id,
            'location'                  =>  $user->userData->location,
            'longitude'                      =>  $user->userData->longitude,
            'latitude'                      =>  $user->userData->latitude,
            'status'                    => $user->userData->status
        ];
        return $this->sendResponse($success, 'Driver status updated successfully.');
    }

    public function checkUserStatus(Request $request){
        $user = Auth::user();
        $user_data = UserData::where('user_id', $user->id)->get()->first();
        $success['user'] = [
            'id' => $user->id,
            'name' => $user->name,
            'email'=>$user->email,
            'phone' => $user->phone,
            'type' => $user->type,
            'active'=>$user->active,
            'active_text'=> $user->getActiveType(),
            'revision' => $user_data->revision,
            'revision_text' => $user_data->getRevision(),
            'status' => $user_data->status,
            'vip' => $user_data->vip,
        ];
        return $this->sendResponse($success, 'Driver status updated successfully.');

    }
    public function arrived(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'order_id'=>'required|exists:orders,id',
              'arrived_to_dropoff'=>'required|bool',
              'arrived_to_pickup'=>'required|bool',
              'driver_id'=>'required|exists:users,id'
            ]);

  if ($validator->fails()) {
            $errors = $validator->errors();
            $msgs = [];
            foreach ($errors->all() as  $ind => $message) {
                array_push($msgs, $message);
            }
            return $this->sendError('Validation Error.', $msgs);
        }
        if($request->arrived_to_dropoff)
        {
            $text='the driver arrived_to_dropoff';
        }
        else if($request->arrived_to_pickup)
        {
           $text='the driver arrived_to_pickup';
        }

    $order = Order::find($request->order_id);
     $user=User::find($order->user_id);
$data=
[
    'title' =>'arrived',
    'body' =>$text,
     'target' =>'customer',
     'link'=>route('admin.orders.index'),
     'target_id'=>$user->name,
    'sender'=>$request->driver_id,
    ];
    try
    {
      $response =Notification::send($user, new LocalNotification($data));

     dd($response);
       foreach ($response as $notifiableResponse) {
           if (!$notifiableResponse->isSent()) {
               throw new CouldNotSendNotification($notifiableResponse->getError());
           }
       }
   } catch (CouldNotSendNotification $e) {
       // Handle the exception
       echo $e->getMessage();
   }

 return $this->sendResponse($data, 'Data send to user successfully');
}

   /**
 * Check whether a driver is attached to a shipping company.
 *
 * @param  int  $driver_id
 * @return \Illuminate\Http\JsonResponse
 */
public function driverCompanyRelation($driver_id)
{
    // 1) make sure the user exists and is a driver
    $driver = User::where('type', 'driver')->find($driver_id);
    if (! $driver) {
        return $this->sendError('Data not found.', ['Driver not exists']);
    }

    // 2) pull the driver’s extra data row
    $driverData = UserData::where('user_id', $driver->id)->first();
    if (! $driverData) {
        return $this->sendError('Data not found.', ['Driver profile missing']);
    }

    // 3) decide if company-linked or not
    $isRelated   = ! is_null($driverData->company_id);
    $companyInfo = null;

    if ($isRelated) {
        $company = User::where('type', 'driverCompany')
                       ->find($driverData->company_id);
        if ($company) {
            $companyInfo = [
                'company_id' => $company->id,
                'name'       => $company->name,
                'phone'      => $company->phone,
                'email'      => $company->email,
            ];
        }
    }

    return $this->sendResponse([
        'driver_id'   => $driver->id,
        'driver_name' => $driver->name,
        'is_related'  => $isRelated,          // true = belongs to a company
        'company'     => $companyInfo         // null when not related
    ], 'Company relationship check completed.');
}


}
