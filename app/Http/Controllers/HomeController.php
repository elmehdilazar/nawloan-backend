<?php

namespace App\Http\Controllers;

use App\Models\CodeScretModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
            if(Auth::user()->type == 'superadministrator' || Auth::user()->type =='admin' || Auth::user()->type =='emp'){
           
                 return view('admin.index');
            }
            else {
              return redirect()->to('index.html');
            }
          
    }
    
//

//read QR Code And Update Status
public function readAndUpdate(Request $req){
  $reqBody=$req->data;    
  $data=  CodeScretModel::where("code", $reqBody)->get();
  $request=json_decode($data[0]['json']);
$id=$request->order_id;


    
        $order = Order::find($id);
        if (!$order) {
			$msgs=['Order not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
        DB::beginTransaction();
       
        $order = Order::with(['accountant','statuses'])->find($id);
        

    
        $user = User::find($order->user_id);
            if ( $request->status != 'cancel') {
             $order->update([
            'status' => $request->status,
        ]);
        OrderStatus::create([
            'user_id' => $request->user_id,
            'order_id' => $order->id,
            'distance'=>$request->distance,
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

        $message = Lang::get('site.not_approve_order_msg') . ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' ' . auth()->user()->name;
        $title = Lang::get('site.not_approve_order');
        if ($order->status == 'approve') {
            $message = Lang::get('site.not_approve_order_msg') . ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' ' . auth()->user()->name;
            $title = Lang::get('site.not_approve_order');
            
        } elseif ($order->status == 'pick_up') {
            $message = Lang::get('site.not_pick_up_order_msg') . ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' ' . auth()->user()->name;
            $title = Lang::get('site.not_pick_up_order');
        } elseif ($order->status == 'delivered') {
            $message = Lang::get('site.not_delivered_order_msg') . ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' ' . auth()->user()->name;
            $title = Lang::get('site.not_delivered_order');
            $orderstatus=OrderStatus::where('order_id',$order->id)->get();
                $pick_up=null;
                $delivered=null;
            foreach($orderstatus as $status){
                if($status->status=='pick_up'){
                    $pick_up=$status->created_at;
                }
                if($status->status=='delivered'){
                    $delivered=$status->created_at;
                }
                $workHours=ceil((int)(strtotime($delivered) - strtotime($pick_up)) / (60 * 60) ) . '';
                $driver=UserData::where('user_id',$order->service_provider)->get()->first();
                $driver->update(['works_hours'=>$workHours]);
            }
        } elseif ($order->status == 'cancel') {
            $message = Lang::get('site.not_cancel_order_msg') . ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' ' . auth()->user()->name;
            $title = Lang::get('site.not_cancel_order');
        } elseif ($order->status == 'pend') {
            $message = Lang::get('site.not_pend_order_msg') . ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' ' . auth()->user()->name;
            $title = Lang::get('site.not_pend_order');
        } elseif ($order->status == 'complete') {
            $message = Lang::get('site.not_complete_order_msg') . ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' ' . auth()->user()->name;
            $title = Lang::get('site.not_complete_order');
            $driver=UserData::where('user_id',$order->service_provider)->get()->first();
            $driver->update(['status'=>'available']);
        }
        $data = [
            'title' => $request->status,
            'body' => 'add_body',
            'target' => 'order',
            'link'  => route('admin.orders.index', ['number' => $order->id]),
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
            if (!empty($user->fcm_token) && $user->id != auth()->user()->id) {
                Notification::send($user, new FcmPushNotification($title, $message, [$user->fcm_token]));
            }
        }
        DB::commit();
            $success['order'] =  $order;
            
        $data=  CodeScretModel::where("code", $reqBody)->delete();

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,"http://194.164.72.247:3120/status");
 
$data = http_build_query($request);

  curl_setopt($ch,CURLOPT_POST, 1);                //0 for a get request
  curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
  curl_setopt($ch,CURLOPT_TIMEOUT, 20);
 


$server_output = curl_exec($ch);

curl_close($ch);

   return redirect()->to('https://nawloan.net/order-offers/'.$order->id);  
 }
 
}
