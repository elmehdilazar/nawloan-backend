<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PayTransaction;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserData;
use App\Models\Order;
use App\Models\Offer;
use App\Notifications\LocalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class PayTransactionController extends BaseController
{
    public function index()
    {
        $trans =Transaction::with(['user', 'order', 'payTransaction','payMethod'])->get();
        $success['count'] =  $trans->count();
        $success['Transactions'] =  $trans;
        return $this->sendResponse($success, 'Transactions information.');
    }
/*  public function store(Request $request)
{
    // ✅ Step 1: Validate Input Data
    $validator = Validator::make($request->all(), [
        'order_id'          =>  'required|exists:orders,id',
        'user_id'           =>  'required|exists:users,id',
        'transaction_id'    =>  'required|unique:pay_transactions,transaction_id',
        'amount'            =>  'required|numeric|min:0',
        'fee'               =>  'required|numeric|min:0',
        'currency'          =>  'required|string',
        'payment_type'      =>  'required|string',
        'status'            =>  'required|string',
        'payment_method_id' =>  'required|exists:payment_methods,id',
        'name_in_card'      =>  'required|string',
        'receiver_name'     =>  'nullable|string',
        'receiver_account'  =>  'nullable|string',
        'notes'             =>  'nullable|string',
    ]);

    if ($validator->fails()) {
        return $this->sendError('Validation Error.', $validator->errors()->all());
    }

    DB::beginTransaction();

    try {
        // ✅ Step 2: Create PayTransaction Record
        $payTran = PayTransaction::create([
            'transaction_id'    =>  $request->transaction_id,
            'amount'            =>  $request->amount,
            'fee'               =>  $request->fee,
            'currency'          =>  $request->currency,
            'payment_type'      =>  $request->payment_type,
            'status'            =>  $request->status,
            'payment_method_id' =>  $request->payment_method_id,
            'name_in_card'      =>  $request->name_in_card,
            'order_id'          =>  $request->order_id,
        ]);

        // ✅ Step 3: Create Transaction Record
        $tran = Transaction::create([
            'order_id'              =>  $request->order_id,
            'user_id'               =>  $request->user_id,
            'pay_transaction_id'    =>  $payTran->id,
            'price'                 =>  $request->amount,
            'payment_method_id'     =>  $request->payment_method_id,
        ]);

        // ✅ Step 4: Update User Balance
        $user = User::find($request->user_id);
        
        $userData = UserData::where('user_id', $user->id)->first();

        if ($userData) {
            $newBalance = $userData->balance + $request->amount;
            $outstanding_balance = $newBalance - $userData->outstanding_balance;

            if ($outstanding_balance >= 0) {
                $userData->update(['balance' => $outstanding_balance, 'outstanding_balance' => 0]);
             
                $user->update(['active' => 1]);  
            } else {
                $userData->update(['balance' => 0, 'outstanding_balance' => abs($outstanding_balance)]);
            }
        }
        

        // ✅ Step 5: Send Notifications
        $notificationData = [
            'title' => 'New Payment Received',
            'body' => 'A new payment of ' . $request->amount . ' ' . $request->currency . ' has been made.',
            'target' => 'order',
            'link'  => route('admin.transactions.index', ['transaction_id' => $payTran->transaction_id]),
            'target_id' => $request->order_id,
            'sender' => $user->name,
        ];

        Notification::send($user, new LocalNotification($notificationData));

        $admins = User::whereIn('type', ['admin', 'superadministrator'])->get();
        foreach ($admins as $admin) {
            Notification::send($admin, new LocalNotification($notificationData));
        }

        DB::commit();

        return $this->sendResponse([
            'payTransaction' =>  $payTran,
            'transaction'    =>  $tran
        ], 'Payment Transaction created successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        return $this->sendError('Transaction Failed.', [$e->getMessage()]);
    }
}*/
public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'order_id'          =>  'required|exists:orders,id',
        'user_id'           =>  'required|exists:users,id',
        'transaction_id'    =>  'required|unique:pay_transactions,transaction_id',
        'amount'            =>  'required|numeric|min:0',
        'fee'               =>  'required|numeric|min:0',
        'currency'          =>  'required|string',
        'payment_type'      =>  'required|string',
        'status'            =>  'required|string',
        'payment_method_id' =>  'required|exists:payment_methods,id',
        'name_in_card'      =>  'required|string',
        'receiver_name'     =>  'nullable|string',
        'receiver_account'  =>  'nullable|string',
        'notes'             =>  'nullable|string',
    ]);

    if ($validator->fails()) {
        return $this->sendError('Validation Error.', $validator->errors()->all());
    }

    DB::beginTransaction();

    try {
        $payTran = PayTransaction::create([
            'transaction_id'    =>  $request->transaction_id,
            'amount'            =>  $request->amount,
            'fee'               =>  $request->fee,
            'currency'          =>  $request->currency,
            'payment_type'      =>  $request->payment_type,
            'status'            =>  $request->status,
            'payment_method_id' =>  $request->payment_method_id,
            'name_in_card'      =>  $request->name_in_card,
            'order_id'          =>  $request->order_id,
        ]);

        $tran = Transaction::create([
            'order_id'              =>  $request->order_id,
            'user_id'               =>  $request->user_id,
            'pay_transaction_id'    =>  $payTran->id,
            'price'                 =>  $request->amount,
            'payment_method_id'     =>  $request->payment_method_id,
        ]);

        $user = User::with('userData')->find($request->user_id);
        $order = Order::with('accountant')->find($request->order_id);

        if ($user && $user->userData && $order) {
            $userData = $user->userData;
            $paidAmount = $request->amount;
            $outstanding = $userData->outstanding_balance;

            if ($paidAmount >= $outstanding) {
                $remaining = $paidAmount - $outstanding;

                $userData->update([
                    'balance' => $remaining,
                    'outstanding_balance' => 0,
                ]);

                $user->update(['active' => 1]);

                // Settlement with driver
                if ($order->service_provider) {
                    $driver = User::with('userData')->find($order->service_provider);
                    if ($driver && $driver->userData) {
                        $driverData = $driver->userData;
                        $driverData->update([
                            'pending_balance' => max(0, $driverData->pending_balance - $outstanding),
                            'balance' => $driverData->balance + $outstanding,
                        ]);
                    }
                }
            } else {
                $userData->update([
                    'balance' => 0,
                    'outstanding_balance' => $outstanding - $paidAmount,
                ]);
            }
        }

        $notificationData = [
            'title' => 'New Payment Received',
            'body' => 'A new payment of ' . $request->amount . ' ' . $request->currency . ' has been made.',
            'target' => 'order',
            'link'  => route('admin.transactions.index', ['transaction_id' => $payTran->transaction_id]),
            'target_id' => $request->order_id,
            'sender' => $user->name ?? 'System',
        ];

        Notification::send($user, new LocalNotification($notificationData));

        $admins = User::whereIn('type', ['admin', 'superadministrator'])->get();
        foreach ($admins as $admin) {
            Notification::send($admin, new LocalNotification($notificationData));
        }

        DB::commit();

        return $this->sendResponse([
            'payTransaction' =>  $payTran,
            'transaction'    =>  $tran
        ], 'Payment Transaction created successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        return $this->sendError('Transaction Failed.', [$e->getMessage()]);
    }
}


    public function showByOrder($id)
    {
        $transaction = Transaction::with(['user', 'order', 'payTransaction','payMethod'])->where('order_id', $id)->get();
        $success['count'] =  $transaction->count();
        $success['transaction'] =  $transaction;
        return $this->sendResponse($success, 'Payment Transactions.');
    }
    public function showByUser($id)
    {
        
        $user=User::where('type','user')->find($id);
        if (!$user) {
			$msgs=['Customer not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
        $transaction = Transaction::with(['payTransaction','order'])->where('user_id', $user->id)->latest()->get();
        $trans=[];
        $orders_ids=[];
        $count=0;
        foreach($transaction as $tran){
        $order = Order::find($tran->order_id);
        $offer = Offer::find($order->offer_id);
        if(!in_array($order->id,$orders_ids)){
       /*       if($offer != null)
            {
                $total_Price=$offer->sub_total;
            }
            else{
                $total_Price= number_format($order->total_price,2);
            }
            */
        $data=[
                'id'                    =>  $order->id,
                'payment_method_id'     =>  $tran->payment_method_id,
                'payment_status'                =>  $tran->payTransaction->status,
                'pick_up_address'       =>  $order->pick_up_address,
               
                'drop_of_address'       =>  $order->drop_of_address,
                'total_price'           =>  number_format($order->total_price,2),
                'order_status'          =>  $order->status,
                'created_at'          =>  date('Y-m-d H:i:s', strtotime($order->created_at)),
            ];
            array_push($trans,$data);
            array_push($orders_ids,$order->id);
            $count++;
            
        }
        }
        $balance=$user->userData->balance;
        $success['count'] =  $count;
        $success['aviable_balance'] =    $balance;
        $success['transaction'] =  $trans;
        return $this->sendResponse($success, 'Payment Transactions.');
    }
    public function showByFactory($id)
    {
        $user=User::where('type','factory')->find($id);
        if (!$user) {
			$msgs=['Customers Company not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
        $count=0;
        $trans=[];
        $orders=Order::where('user_id',$user->id)->get();
        foreach($orders as $order){
        $transaction = Transaction::with('payTransaction')->where('user_id', $user->id)->where('order_id',$order->id)->latest()->get()->first();
       // foreach($transaction as $tran){
        $offer = Offer::find($order->offer_id);
        $data['id']                    =  $order->id;
        if($transaction){
            
                $data['payment_method_id'] =  $transaction->payment_method_id;
                $data['payment_status']   =  $transaction->payTransaction->status;
        }
                    else{
                        $data['payment_method_id']     = null;
                        $data['payment_status']        =  'not_paid';
                        
                    }
                $data['pick_up_address']       =  $order->pick_up_address;
                $data['drop_of_address']       =  $order->drop_of_address;
                if($offer){
                    $data['total_price']           =  number_format($offer->sub_total,2);
                }else{
                    $data['total_price'] =0.00;
                }
                $data['order_status']          =  $order->status;
                $data['created_at']          =  date('Y-m-d H:i:s', strtotime($order->created_at));
            array_push($trans,$data);
            $count++;
        //}
            
        }
        $balance=$user->userData->balance;
        $outstanding_balance=$user->userData->outstanding_balance;
        $success['count'] =  $count;
        $success['aviable_balance'] =    number_format($balance,2);
        $success['outstanding_balance']=number_format($outstanding_balance,2);
        $success['transaction'] =  $trans;
        return $this->sendResponse($success, 'Payment Transactions.');
    }
    public function showByDriver($id)
    {
        $driver=User::select('id')->where('type','driver')->find($id);
        if (!$driver) {
			$msgs=['Driver not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
        $orders=Order::where('service_provider',$driver->id)->latest()->get();
        
        $trans=[];
        $count=0;
        foreach($orders as $order){
            
            $transaction = Transaction::with('payTransaction')->where('order_id', $order->id)->get()->first();
            $data=[
                    'id'                    =>  $order->id,
                    'pick_up_address'       =>  $order->pick_up_address,
                    'drop_of_address'       =>  $order->drop_of_address,
                    'total_price'           =>  number_format($order->accountant->service_provider_amount -( $order->accountant->service_provider_amount * 5) /100,2),
                    'order_status'          =>  $order->status,
                    ];
                    if($transaction){
                        $data['payment_method_id']     =  $transaction->payment_method_id;
                        $data['payment_status']        =  $transaction->payTransaction->status;
                    }
                    else{
                        $data['payment_method_id']     = null;
                        $data['payment_status']        =  'not_paid';
                        
                    }
                        $data['created_at']= date('Y-m-d H:i:s', strtotime($order->created_at));
                array_push($trans,$data);
                $count++;
            
        }
        $balance=$driver->userData->balance;
        
        $pending_balance=$driver->userData->pending_balance;
        $success['count'] =  $count;
        $success['aviable_balance'] =    number_format($balance,2);
        $success['pending_balance']=number_format($pending_balance,2);
        $success['total_balance']=number_format($pending_balance+$balance,2);
        $success['transaction'] =  $trans;
        return $this->sendResponse($success, 'Payment Transactions.');
    }
    public function showByDriverCompany($id)
    {
        $company=User::select('id')->where('type','driverCompany')->find($id);
        if (!$company) {
			$msgs=['Shipping Company not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
        $usersD=UserData::select('user_id')->where('company_id',$company->id)->get();
        $users=[];
        foreach($usersD as $usersDa){
            $user=User::select('id')->find($usersDa->user_id);
            $ids=$user->id;
            array_push($users,$ids);
        }    
        $offers=Offer::select('order_id')->where('status','!=','cancel')->where('user_id',$company->id)->get();
        $orders1=[];
        foreach($offers as $offer){
            $order=Order::find($offer->order_id);
            $ids=$order->id;
            array_push($orders1,$ids);
        }
/*        $drivers=User::select('id')->whereHas('userData',function($q) use ($driver){
            return $q->where('company_id',$driver->id)->get();
        })->get();
  */      
       // $orders=Order::select('id')->where('service_provider',$driver->id)->get();
        $transaction = Transaction::with('payTransaction')->whereIn('order_id', $orders1)->latest()->get();
          // return $this->sendResponse($orders1, 'Payment Transactions.'); 
        $trans=[];
        $count=0;
        
        foreach($transaction as $tran){
        $order = Order::find($tran->order_id);
        $data=[
                'id'                    =>  $order->id,
                'payment_method_id'     =>  $tran->payment_method_id,
                'payment_status'                =>  $tran->payTransaction->status,
                'pick_up_address'       =>  $order->pick_up_address,
                'drop_of_address'       =>  $order->drop_of_address,
                'total_price'           =>  number_format(( $order->accountant->service_provider_amount * 5) /100 , 2),
                'order_status'          =>  $order->status,
                'created_at'          =>  date('Y-m-d H:i:s', strtotime($order->created_at)),
            ];
            array_push($trans,$data);
            $count++;
        }
        $balance=$company->userData->balance;

        $pending_balance=$company->userData->pending_balance;
        $outstanding_balance=$company->userData->outstanding_balance;
        $total_balance=$balance+$pending_balance;
        $success['count'] =  $count;
        $success['aviable_balance'] =    number_format($balance,2);
        $success['pending_balance']=number_format($pending_balance,2);
        $success['total_balance']=number_format($total_balance,2);
        $success['transaction'] =  $trans;
        return $this->sendResponse($success, 'Payment Transactions.');
    }
    
     public function saveFawryStellement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'           =>  'required|exists:users,id',
            'transaction_id'    =>  'required|unique:pay_transactions,transaction_id',
            'amount'            =>  'required',
            'fee'               =>  'required',
            'currency'          =>  'required',
            'payment_type'      =>  'required',
            'status'            =>  'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            $msgs = [];
            foreach ($errors->all() as  $ind => $message) {
                array_push($msgs, $message);
            }
            return $this->sendError('Validation Error.', $msgs);
        }
        DB::beginTransaction();
        $payTran=   PayTransaction::create([
            'transaction_id'    =>  $request->transaction_id,
            'amount'            =>  $request->amount,
            'fee'               =>  $request->fee,
            'currency'          =>  $request->currency,
            'payment_type'      =>  $request->payment_type,
            'status'            =>  $request->status,
            'payment_method'    =>  $request->payment_method,
            'name_in_card'      =>  $request->name_in_card,
            'order_id'          =>  null,
            'payment_perpose'   =>  "stellement_pay",
        ]);
        //   $tran   =   Transaction::create([
        //     'order_id'              =>  $request->order_id,
        //     'user_id'               =>  $request->user_id,
        //     'pay_transaction_id'    =>  $payTran->id,
        //     'price'                 =>  $request->amount,
        //     'payment_method_id'    =>  1,
        // ]);
        
        $user1 = User::find($request->user_id);
        $userData=UserData::where('user_id',$user1->id)->first();
        
                $userData->update(['outstanding_balance'=> 0]);
            
        $data = [
            'title' => 'new_payment',
            'body' => 'edit_body',
            'target' => 'stellement Pay '. $request->amount ." EGP",       
            'target_id' =>null,

            'link'  => route('admin.transactions.index', ['transaction_id' => $payTran->transaction_id]),
            'sender' => $user1->name,
        ];
      
        Notification::send($user1, new LocalNotification($data));
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }
        DB::commit();
        $success['payTransaction'] =  $payTran;
        return $this->sendResponse($success, 'Pay Transaction created successfully.');
    }

}
