<?php

namespace App\Http\Controllers\API\Auth;

use App\Models\Offer;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\SupportCenter;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Message;
use App\Models\User;
use App\Models\BankInfo;
use App\Models\UserData;
use App\Models\ResetCodePassword;
use App\Notifications\LocalNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Pnlinh\InfobipSms\Facades\InfobipSms;
use Intervention\Image\Facades\Image;
use Illuminate\Validation\Rule;
use Exception;

class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */

    public function registerUser(Request $request)
    {
        /*add by mohammed*/
        $userphone = User::where('phone', $request->phone)->where('phone_verified_at',null)->first();
        if ($userphone) {
            $userDataPhone = UserData::where('user_id', $userphone->id)->first();
            $order = Order::where('user_id',$userphone->id)->get();
            foreach ($order as $orders) {
                $offer = Offer::where('order_id',$orders->id)->get();
                foreach ($offer as $offer) {
                    $offer->delete();
                }
                $orderStatus = OrderStatus::where('order_id',$orders->id)->get();
                foreach ($orderStatus as $orderStatus) {
                    $orderStatus->delete();
                }
                $orders->delete();
            }
            if ($userDataPhone){
                $userDataPhone->delete();
            }
            $userphone->delete();
        }
        $useremail = User::where('email', $request->email)->where('email',!null)->where('phone_verified_at',null)->first();
        if ($useremail){
            $userDataEmail = UserData::where('user_id', $useremail->id)->get()->first();
            $order = Order::where('user_id',$userphone->id)->get();
            foreach ($order as $orders) {
                $offer = Offer::where('order_id',$orders->id)->get();
                foreach ($offer as $offer) {
                    $offer->delete();
                }
                $orderStatus = OrderStatus::where('order_id',$orders->id)->get();
                foreach ($orderStatus as $orderStatus) {
                    $orderStatus->delete();
                }
                $orders->delete();
            }
            if ($userDataEmail){
                $userDataEmail->delete();
            }
            $useremail->delete();
        }
        /*end*/
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30|unique:users,phone',
            'email'=>'nullable|email|unique:users,email',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'image' => 'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
        ],[
            'phone.unique' => trans("validation.Account Already Exists"),
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
        $input['image'] = 'uploads/users/default.png';
        if ($request->image) {
            Image::make($request->image)
                ->save(public_path('uploads/users/' . $request->image->hashName()));
            $input['image'] = 'uploads/users/' . $request->image->hashName();
        } //end of if
        $input['password'] = bcrypt($input['password']);
           if($request->has('email'))
        {
            $email= $input['email'];
        }
        else{

            $email=null;
        }
        DB::beginTransaction();
        $user = User::create([
            'name' => $input['name'],
            'email' =>    $email,
            'phone' =>  $input['phone'],
            'password' =>  $input['password'],
            'user_type' =>  'service_seeker',
            'type' =>  'user',
        ]);
        $userData = UserData::create(['image' => $request->image, 'user_id' => $user->id, 'type' => 'user']);

        $success['token'] =  $user->createToken('MyApp')->accessToken;

        $success['user'] = [   
            'id' =>  $user->id,
            'name' => $user->name,
            'email'=> $user->email, 
            'phone' => $user->phone, 
            'image' => $userData->image,
            'status'=> $user->active,
            'type' => $user->type, // Added By Muhammad-AF
            'phone_verified_at'=> $user->phone_verified_at,
            'created_at'=>date('Y-m-d H:i A', strtotime($user->created_at))
        ];
        $data = [
            'title' => 'the_register',
            'body' => 'register_body',
            'target' => 'customer',
            'link'  => route('admin.customers.index', ['name' => $user->name]),
            'target_id' => $user->name,
            'sender' => $user->name,
        ];
        $users = User::where('user_type', 'manage')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }
        DB::commit();
        $otp_url = 'To verify phone via otp , required send phone number in body, use post method : ' . env('APP_URL') . 'api/auth/sendOtp';
        $success['otp_url'] = $otp_url;

        return $this->sendResponse($success, 'User register successfully.');
    }

    public function registerUserImage(Request $request,$id)
    {
        $user = User::with('userData')->find($id);
        if (!$user) {
            $msgs=['Customer not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
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
        $userData = UserData::where('user_id',$user->id)->first();
        if ($userData->image == null){  // add if by mohammed
            $input['image'] = 'uploads/users/default.png';
        }
        if ($request->file('image')) {
            Image::make($request->image)->save(public_path('uploads/users/' . $request->image->hashName()));
            $input['image'] = 'uploads/users/' . $request->image->hashName();
        }elseif (!$request->file('image') && $userData->image != null){ // add elseif by mohammed
            $input['image'] = $userData->image;
        }

        DB::beginTransaction();
        /*$userData = UserData::where('user_id',$user->id)->first();*/  // commit by mohammed
        $userData->update([
            'image'                     =>  $input['image'],
        ]);
        //  $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['user'] = [
            'id' =>  $user->id,
            'name'              =>  $user->name,
            'email'     =>$user->email,
            'phone' => $user->phone,
            'status'            =>  $user->active,
            'phone_verified_at' =>  $user->phone_verified_at,
            'created_at'        =>  date('Y-m-d H:i A', strtotime($user->created_at)),
            'image' => $userData->image,
        ];
        DB::commit();
        return $this->sendResponse($success, 'User image uploaded successfully.');
    }
    public function registerFactory(Request $request)
    {
        /*add by mohammed*/
        $userphone = User::where('phone', $request->phone)->where('phone_verified_at',null)->first();
        if ($userphone) {
            $userDataPhone = UserData::where('user_id', $userphone->id)->first();
            $order = Order::where('user_id',$userphone->id)->get();
            foreach ($order as $orders) {
                $offer = Offer::where('order_id',$orders->id)->get();
                foreach ($offer as $offer) {
                    $offer->delete();
                }
                $orderStatus = OrderStatus::where('order_id',$orders->id)->get();
                foreach ($orderStatus as $orderStatus) {
                    $orderStatus->delete();
                }
                $orders->delete();
            }
            if ($userDataPhone){
                $userDataPhone->delete();
            }
            $userphone->delete();
        }
        $useremail = User::where('email', $request->email)->where('email',!null)->where('phone_verified_at',null)->first();
        if ($useremail){
            $userDataEmail = UserData::where('user_id', $useremail->id)->get()->first();
            $order = Order::where('user_id',$userphone->id)->get();
            foreach ($order as $orders) {
                $offer = Offer::where('order_id',$orders->id)->get();
                foreach ($offer as $offer) {
                    $offer->delete();
                }
                $orderStatus = OrderStatus::where('order_id',$orders->id)->get();
                foreach ($orderStatus as $orderStatus) {
                    $orderStatus->delete();
                }
                $orders->delete();
            }
            if ($userDataEmail){
                $userDataEmail->delete();
            }
            $useremail->delete();
        }
        /*end*/
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email'=>'nullable|email|unique:users,email',
            'phone' => 'required|string|max:30|unique:users,phone',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'commercial_record' => 'required|string|max:30|unique:user_data,commercial_record',
            'tax_card' => 'required|string|max:30|unique:user_data,tax_card',
            'location'=>'nullable|string',
            'longitude' => 'nullable',
            'latitude'=>'nullable',
        ],[
            'phone.unique' => trans("validation.Account Already Exists"),
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
        $input['password'] = bcrypt($input['password']);
        $user1 = null;
        
        if($request->has('email'))
        {
            $email= $input['email'];
        }
        else{

            $email=null;
        }
        DB::beginTransaction();
        $user = User::create([
            'name' => $input['name'],
            'email'=>$email,
            'phone' =>  $input['phone'],
            'password' =>  $input['password'],
            'user_type' =>  'service_seeker',
            'type' =>  'factory',
        ]);
        $userData=UserData::create([
            'commercial_record'     =>      $request->commercial_record,
            'tax_card'              =>      $request->tax_card,
            'location'              =>      $request->location,
            'longitude'             =>      $request->longitude,
            'latitude'              =>      $request->latitude,
            'type'                  =>      'factory',
            'phone'                 =>      $user->phone,
            'user_id'               =>      $user->id
        ]);
        $user1 = User::with(['userData', 'bank'])->find($user->id);
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $userInfo = [
            'id'                        =>      $user1->id,
            'name'                      =>      $user1->name,
            'email'                     =>      $user1->email,
            'phone'                     =>      $user1->phone,
            'status'                    =>      $user1->active,
            'type'                      =>      $user1->type, //Added By Muhammad-AF
            'phone_verified_at'         =>      $user->phone_verified_at,
            'created_at'                =>      date('Y-m-d H:i A', strtotime($user->created_at)),
            'image'                     =>      $user1->userData->image,
            'commercial_record'         =>      $user1->userData->commercial_record,
            'commercial_record_image_f' =>      $user1->userData->commercial_record_image_f,
            'commercial_record_image_b' =>      $user1->userData->commercial_record_image_b,
            'tax_card'                  =>      $user1->userData->tax_card,
            'tax_card_image_f'          =>      $user1->userData->tax_card_image_f,
            'tax_card_image_b'          =>      $user1->userData->tax_card_image_b,
            'location'                  =>      $user1->userData->location,
            'longitude'                      =>      $user1->userData->longitude,
            'latitude'                      =>      $user1->userData->latitude,
        ];
        if (!empty($user->bank)) {
            $userInfo['bank_name']   =      $user1->bank->bank_name;
            $userInfo['branch_name']                   =      $user1->bank->branch_name;
            $userInfo['account_holder_name']           =      $user1->bank->account_holder_name;
            $userInfo['account_number']                =      $user1->bank->account_number;
            $userInfo['soft_code']                     =      $user1->bank->soft_code;
            $userInfo['iban']                          =      $user1->bank->iban;
        }
        $success['user'] = $userInfo;
        $data = [
            'title' => 'the_register',
            'body' => 'register_body',
            'target' => 'factory',
            'link'  => route('admin.factories.index', ['name' => $user->name]),
            'target_id' => $user->name,
            'sender' => $user->name,
        ];
        $users = User::where('user_type', 'manage')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }
        DB::commit();
        $otp_url = 'To verify phone via otp , required send phone number in body, use post method : ' . env('APP_URL') . 'api/auth/sendOtp';
        $success['otp_url'] = $otp_url;
        return $this->sendResponse($success, 'Factory register successfully.');
    }
    public function registerFactoryInfo(Request $request,$id)
    {
        $user = User::with('userData')->find($id);
        if (!$user) {

            $msgs=['Customer Comapny not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
        $validator = Validator::make($request->all(), [
            'commercial_record'   =>  ['required', Rule::unique('user_data')->ignore($user->id,'user_id'),],
            'commercial_record' => 'string|max:255',
            'tax_card'   =>  ['required', Rule::unique('user_data')->ignore($user->id,'user_id'),],
            'tax_card' => 'string|max:255',
            'location'=>'nullable|string',
            'longitude' => 'nullable',
            'latitude'=>'nullable',
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
        $input = $request->all();
        $input['image'] = 'uploads/users/default.png';

        DB::beginTransaction();
        $userData = UserData::where('user_id',$user->id)->first();
        $userData->update([
            'commercial_record' =>  $input['commercial_record'],
            'tax_card' =>  $input['tax_card'],
            'location'                =>  $input['location'],
            'longitude'                =>  $input['longitude'],
            'latitude'              =>  $input['latitude']
        ]);
        $userBank=BankInfo::where('user_id',$user->id)->first();

        if ($request->has('bank_name') && $request->has('branch_name') &&  $request->has('account_holder_name') && $request->has('account_number')) {
            if($userBank){
                $userBank ->update([
                    'user_id'                   =>      $user->id,
                    'bank_name'                 =>      $input['bank_name'],
                    'branch_name'               =>      $input['branch_name'],
                    'account_holder_name'       =>      $input['account_holder_name'],
                    'account_number'            =>      $input['account_number'],
                    'soft_code'                 =>      $input['soft_code'],
                    'iban'                      =>      $input['iban'],
                ]);
            }
            else{
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

        $user1 = User::with(['userData', 'bank'])->find($user->id);
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $userInfo = [
            'id'                        =>      $user1->id,
            'name'                      =>      $user1->name,
            'email'                     =>      $user1->email,
            'phone'                     =>      $user1->phone,
            'status'                    =>      $user1->active,
            'phone_verified_at'         =>      $user->phone_verified_at,
            'created_at'                =>      date('Y-m-d H:i A', strtotime($user->created_at)),
            'image'                     =>      $user1->userData->image,
            'commercial_record'         =>      $user1->userData->commercial_record,
            'commercial_record_image_f' =>      $user1->userData->commercial_record_image_f,
            'commercial_record_image_b' =>      $user1->userData->commercial_record_image_b,
            'tax_card'                  =>      $user1->userData->tax_card,
            'tax_card_image_f'          =>      $user1->userData->tax_card_image_f,
            'tax_card_image_b'          =>      $user1->userData->tax_card_image_b,
            'location'                  =>      $user1->userData->location,
            'longitude'                      =>      $user1->userData->longitude,
            'latitude'                      =>      $user1->userData->latitude,
        ];
        if (!empty($user->bank)) {
            $userInfo['bank_name']   =      $user1->bank->bank_name;
            $userInfo['branch_name']                   =      $user1->bank->branch_name;
            $userInfo['account_holder_name']           =      $user1->bank->account_holder_name;
            $userInfo['account_number']                =      $user1->bank->account_number;
            $userInfo['soft_code']                     =      $user1->bank->soft_code;
            $userInfo['iban']                          =      $user1->bank->iban;
        }
        $success['user'] = $userInfo;
        DB::commit();

        return $this->sendResponse($success, 'Factory information added successfully.');
    }
    public function registerFactoryImage(Request $request,$id)
    {
        $user = User::with('userData')->find($id);
        if (!$user) {
            $msgs=['Customer Comapny not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
        $validator = Validator::make($request->all(), [
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

        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            $msgs = [];
            foreach ($errors->all() as  $ind => $message) {
                array_push($msgs, $message);
            }
            return $this->sendError('Validation Error.', $msgs);
        }
         if ($request->file('image')) {
        $imagePath = $request->image->hashName();
        Image::make($request->image)->save(public_path("uploads/users/{$imagePath}"));
        $input['image'] = "uploads/users/{$imagePath}";
    } else {
        $input['image'] = $userData->image ?? 'uploads/users/default.png';
    }
        $input = $request->all();
        $userData = UserData::where('user_id',$user->id)->first();
        if ($userData->image == null){ // add if by mohammed
            $input['image'] = 'uploads/users/default.png';
        }
        if ($request->image) {
            Image::make($request->image)
                ->save(public_path('uploads/users/' . $request->image->hashName()));
            $input['image'] = 'uploads/users/' . $request->image->hashName();
        }elseif (!$request->image && $userData->image != null){  // add elseif by mohamed
            $input['image'] = $userData->image;
        }
        $input['commercial_record_image_f'] = $userData->commercial_record_image_f;  // edit by mohammed form $input['commercial_record_image_f'] = null;
        if ($request->commercial_record_image_f) {
            Image::make($request->commercial_record_image_f)
                ->save(public_path('uploads/commercial_records/' . $request->commercial_record_image_f->hashName()));
            $input['commercial_record_image_f'] = 'uploads/commercial_records/' . $request->commercial_record_image_f->hashName();
        }
        $input['commercial_record_image_b'] = $userData->commercial_record_image_b;   // edit by mohammed form $input['commercial_record_image_b'] = null;
        if ($request->commercial_record_image_b) {
            Image::make($request->commercial_record_image_b)
                ->save(public_path('uploads/commercial_records/' . $request->commercial_record_image_b->hashName()));
            $input['commercial_record_image_b'] = 'uploads/commercial_records/' . $request->commercial_record_image_b->hashName();
        }
        $input['tax_card_image_f'] = $userData->tax_card_image_f;      // edit by mohammed form $input['tax_card_image_f'] = null;
        if ($request->tax_card_image_f) {
            Image::make($request->tax_card_image_f)
                ->save(public_path('uploads/tax_cards/' . $request->tax_card_image_f->hashName()));
            $input['tax_card_image_f'] = 'uploads/tax_cards/' . $request->tax_card_image_f->hashName();
        }
        $input['tax_card_image_b'] = $userData->tax_card_image_b;      // edit by mohammed form $input['tax_card_image_b'] = null;
        if ($request->tax_card_image_b) {
            Image::make($request->tax_card_image_b)
                ->save(public_path('uploads/tax_cards/' . $request->tax_card_image_b->hashName()));
            $input['tax_card_image_b'] = 'uploads/tax_cards/' . $request->tax_card_image_b->hashName();
        }

        DB::beginTransaction();
        /*$userData = UserData::where('user_id',$user->id)->first();*/ // commit by mohammed
        $userData->update([
            'image'                             =>  $input['image'],
            'commercial_record_image_f'         =>  $input['commercial_record_image_f'],
            'commercial_record_image_b'         =>  $input['commercial_record_image_b'],
            'tax_card_image_f'                  =>  $input['tax_card_image_f'],
            'tax_card_image_b'                  =>  $input['tax_card_image_b'],
        ]);
        //    $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['user'] = [
            'id' =>  $user->id,
            'name'              =>  $user->name,'email'=>$user->email, 'phone' => $user->phone,
            'status'            =>  $user->active,
            'phone_verified_at' =>  $user->phone_verified_at,
            'created_at'        =>  date('Y-m-d H:i A', strtotime($user->created_at)),
            'image' => $userData->image,
            'commercial_record' => $userData->commercial_record, 'commercial_record_image_f' => $userData->commercial_record_image_f,
            'commercial_record_image_b' => $userData->commercial_record_image_b, 'tax_card' => $userData->tax_card,
            'tax_card_image_f' => $userData->tax_card_image_f, 'tax_card_image_b' => $userData->tax_card_image_b,
            'location'=>$userData->location,
            'longitude' => $userData->longitude,'latitude'=>$userData->latitude, 'type' => $userData->type,
        ];
        DB::commit();
        return $this->sendResponse($success, 'Customer company images uploaded successfully.');
    }
    public function registerDriver(Request $request)
    {
        /*add by mohammed*/
        $userphone = User::where('phone', $request->phone)->where('phone_verified_at',null)->first();
        if ($userphone) {
            $userDataPhone = UserData::where('user_id', $userphone->id)->first();
            $sup_center = SupportCenter::where('user_id', $userphone->id)->get();
            foreach ($sup_center as $center) {
                $center->delete();
            }
            $transaction = Transaction::where('user_id', $userphone->id)->get();
            foreach ($transaction as $transaction) {
                $transaction->delete();
            }

            if ($userDataPhone){
                $userDataPhone->delete();
            }
            $userphone->delete();
        }
        $useremail = User::where('email', $request->email)->where('email',!null)->where('phone_verified_at',null)->first();
        if ($useremail){
            $userDataEmail = UserData::where('user_id', $useremail->id)->get()->first();
            $userDataPhone = UserData::where('user_id', $userphone->id)->first();
            $sup_center = SupportCenter::where('user_id', $userphone->id)->get();
            foreach ($sup_center as $center) {
                $center->delete();
            }
            $transaction = Transaction::where('user_id', $userphone->id)->get();
            foreach ($transaction as $transaction) {
                $transaction->delete();
            }
            if ($userDataEmail){
                $userDataEmail->delete();
            }
            $useremail->delete();
        }
        /*end*/
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email'=>'nullable|email|unique:users,email',
            'phone' => 'required|string|max:30|unique:users,phone',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ],[
            'phone.unique' => trans("validation.Account Already Exists"),
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


        $input['password'] = bcrypt($input['password']);
        DB::beginTransaction();
        $active=0;
        if(isset( $input['company_id']))  $active=1;
        $user = User::create([
            'name' => $input['name'],
            'email'=>$input['email'],
            'phone' =>  $input['phone'],
            'password' =>  $input['password'],
            'user_type' =>  'service_provider',
            'type' =>  'driver',
            'active' => 0, // add by mohammed  to make the driver acount not active when reigster
        ]);
        if(isset( $input['company_id'])){
                UserData::create([
            'national_id'               =>  $input['national_id'],
            'track_type'                =>  $input['track_type'],
            'driving_license_number'    =>  $input['driving_license_number'],
            'track_license_number'      =>  $input['track_license_number'],
            'track_number'              =>  $input['track_number'],
            'company_id'                =>  $input['company_id'],
            'type'=>$user->type,
            'status'=>'available',
            'phone'=>$user->phone,
             'user_id'=>$user->id,
             'revision' => 1  
        ]);
            
        }else{
            
        $userData=UserData::create(['type'=>$user->type,'phone'=>$user->phone,'user_id'=>$user->id]);
        }
       
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['user'] = [
            'id' =>  $user->id,
            'name'              =>  $user->name, 'email'=>$user->email,'phone' => $user->phone,
            'status'            =>  $user->active,
            'phone_verified_at' =>  $user->phone_verified_at,
            'created_at'        =>  date('Y-m-d H:i A', strtotime($user->created_at)),
            'type'             => $user->type,
        ];
        $data = [
            'title' => 'the_register',
            'body' => 'register_body',
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
        $otp_url = 'To verify phone via otp , required send phone number in body, use post method : ' . env('APP_URL') . 'api/auth/sendOtp';
        $success['otp_url'] = $otp_url;
        return $this->sendResponse($success, 'User register successfully.');
    }
    public function registerDriverInfo(Request $request,$id)
    {
        $user = User::with('userData')->find($id);
        if (!$user) {
            $msgs=['Driver not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
        $validator = Validator::make($request->all(), [
            'national_id'   =>  ['required', Rule::unique('user_data')->ignore($user->id,'user_id'),],
            'national_id' => 'string|max:255',
            'track_type' => 'required|exists:cars,id',
            'driving_license_number'        =>   ['required', Rule::unique('user_data')->ignore($user->id,'user_id'),],
            'driving_license_number'        =>   'string|max:255',
            'track_license_number'          =>   ['required', Rule::unique('user_data')->ignore($user->id,'user_id'),],
            'track_license_number'          =>   'string|max:255',
            'track_number'                  =>   ['required', Rule::unique('user_data')->ignore($user->id,'user_id'),],
            'track_number' => 'string|max:255',
            'company_id' => 'nullable',
            'longitude'  =>  'nullable',
            'latitude'  =>  'nullable',
            'location'=>'nullable|string'
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


        DB::beginTransaction();
        $userData = UserData::where('user_id',$user->id)->first();
        $userData->update([
            'national_id'               =>  $input['national_id'],
            'track_type'                =>  $input['track_type'],
            'driving_license_number'    =>  $input['driving_license_number'],
            'track_license_number'      =>  $input['track_license_number'],
            'track_number'              =>  $input['track_number'],
            'company_id'                =>  $input['company_id'],
            'location'                  =>  $input['location'],
            'longitude'                      =>  $input['longitude'],
            'latitude'                      =>  $input['latitude'],
            'type' =>  'driver',
            'revision' => 1   // add by mohammed
        ]);
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['user'] = [
            'id' =>  $user->id,
            'name'              =>  $user->name,'email'=>$user->email, 'phone' => $user->phone,
            'status'            =>  $user->active,
            'phone_verified_at' =>  $user->phone_verified_at,
            'created_at'        =>  date('Y-m-d H:i A', strtotime($user->created_at)),
            'image' => $userData->image,
            'national_id' => $userData->national_id,
            'national_id_image_f' => $userData->national_id_image_f,
            'national_id_image_b' => $userData->national_id_image_b,
            'track_type' => $userData->track_type,
            'driving_license_number' => $userData->driving_license_number,
            'driving_license_image_f' => $userData->driving_license_image_f,
            'driving_license_image_b' => $userData->driving_license_image_b,
            'track_license_number' => $userData->track_license_number,
            'track_license_image_f' => $userData->track_license_image_f,
            'track_license_image_b' => $userData->track_license_image_b,
            'track_number' => $userData->track_number, 'company_id' => $userData->company_id,
            'location'=>$userData->location,
            'longitude' => $userData->longitude,'latitude'=>$userData->latitude, 'type' => $userData->type,
        ];
        $data = [
            'title' => 'the_register',
            'body' => 'register_body',
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

        return $this->sendResponse($success, 'User register successfully.');
    }
    public function registerDriverImage(Request $request,$id)
    {
        $user = User::with('userData')->find($id);
        if (!$user) {
            $msgs=['Driver not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
        $validator = Validator::make($request->all(), [
            'national_id_image_f' => 'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'national_id_image_b' => 'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'image' => 'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'driving_license_image_f' => 'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'driving_license_image_b' => 'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'track_license_image_f' => 'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'track_license_image_b' => 'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'track_image_f' => 'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'track_image_b' => 'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
            'track_image_s' => 'nullable|image|mimes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|
            mimetypes:image/jpeg,jpeg,image/jpg,jpg,image/png,png,image/gif,gif|max:10240',
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
        $userData = UserData::where('user_id',$user->id)->first(); // add by mohammed
        if ($userData->image == null) {     // add if by mohammed
            $input['image'] = 'uploads/users/default.png';
        }
        if ($request->file('image')) {
            Image::make($request->image)->save(public_path('uploads/users/' . $request->image->hashName()));
            $input['image'] = 'uploads/users/' . $request->image->hashName();
        }elseif (!$request->file('image') && $userData->image != null) { // add elseif by mohammed
            $input['image'] = $userData->image;
        }
        $input['national_id_image_f'] = $userData->national_id_image_f;  // edit by mohammed  from $input['national_id_image_f'] = null
        if ($request->file('national_id_image_f')) {
            Image::make($request->national_id_image_f)
                ->save(public_path('uploads/national_ids/' . $request->national_id_image_f->hashName()));
            $input['national_id_image_f'] = 'uploads/national_ids/' . $request->national_id_image_f->hashName();
        }
        $input['national_id_image_b'] = $userData->national_id_image_b;  // edit by mohammed from $input['national_id_image_b'] = null
        if ($request->file('national_id_image_b')) {
            Image::make($request->national_id_image_b)
                ->save(public_path('uploads/national_ids/' . $request->national_id_image_b->hashName()));
            $input['national_id_image_b'] = 'uploads/national_ids/' . $request->national_id_image_b->hashName();
        }
        $input['driving_license_image_f'] = $userData->driving_license_image_f;  // edit by mohammed form $input['driving_license_image_f'] = null
        if ($request->file('driving_license_image_f')) {
            Image::make($request->driving_license_image_f)
                ->save(public_path('uploads/driving_licenses/' . $request->driving_license_image_f->hashName()));
            $input['driving_license_image_f'] = 'uploads/driving_licenses/' . $request->driving_license_image_f->hashName();
        }
        $input['driving_license_image_b'] = $userData->driving_license_image_b;   // edit by mohammed form $input['driving_license_image_b'] = null
        if ($request->file('driving_license_image_b')) {
            Image::make($request->driving_license_image_b)
                ->save(public_path('uploads/driving_licenses/' . $request->driving_license_image_b->hashName()));
            $input['driving_license_image_b'] = 'uploads/driving_licenses/' . $request->driving_license_image_b->hashName();
        }
        $input['track_license_image_f'] = $userData->track_license_image_f;  // edit by mohammed form $input['track_license_image_f'] = null
        if ($request->file('track_license_image_f')) {
            Image::make($request->track_license_image_f)
                ->save(public_path('uploads/truck_licenses/' . $request->track_license_image_f->hashName()));
            $input['track_license_image_f'] = 'uploads/truck_licenses/' . $request->track_license_image_f->hashName();
        }
        $input['track_license_image_b'] = $userData->track_license_image_b;  // edit by mohammed form $input['track_license_image_f'] = null
        if ($request->file('track_license_image_b')) {
            Image::make($request->track_license_image_b)
                ->save(public_path('uploads/truck_licenses/' . $request->track_license_image_b->hashName()));
            $input['track_license_image_b'] = 'uploads/truck_licenses/' . $request->track_license_image_b->hashName();
        }
        $input['track_image_f'] = $userData->track_image_f; // edit by mohammed form $input['track_image_f'] = null
        if ($request->file('track_image_f')) {
            Image::make($request->track_image_f)
                ->save(public_path('uploads/trucks/' . $request->track_image_f->hashName()));
            $input['track_image_f'] = 'uploads/trucks/' . $request->track_image_f->hashName();
        }

        $input['track_image_b'] = $userData->track_image_b;  // edit by mohammed form $input['track_image_b'] = null
        if ($request->file('track_image_b')) {
            Image::make($request->track_image_b)
                ->save(public_path('uploads/trucks/' . $request->track_image_b->hashName()));
            $input['track_image_b'] = 'uploads/trucks/' . $request->track_image_b->hashName();
        }

        $input['track_image_s'] = $userData->track_image_s;     // edit by mohammed form $input['track_image_s'] = null
        if ($request->file('track_image_s')) {
            Image::make($request->track_image_s)
                ->save(public_path('uploads/trucks/' . $request->track_image_s->hashName()));
            $input['track_image_s'] = 'uploads/trucks/' . $request->track_image_s->hashName();
        }

        DB::beginTransaction();
        /*$userData = UserData::where('user_id',$user->id)->first();*/ // commit by mohammed
        $userData->update([
            'image'                     =>  $input['image'],
            'national_id_image_f'       =>  $input['national_id_image_f'],
            'national_id_image_b'       =>  $input['national_id_image_b'],
            'driving_license_image_f'   =>  $input['driving_license_image_f'],
            'driving_license_image_b'   =>  $input['driving_license_image_b'],
            'track_license_image_f'     =>  $input['track_license_image_f'],
            'track_license_image_b'     =>  $input['track_license_image_b'],
            'track_image_f'             =>  $input['track_image_f'],
            'track_image_b'             =>  $input['track_image_b'],
            'track_image_s'             =>  $input['track_image_s'],
        ]);
        // $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['user'] = [
            'id' =>  $user->id,
            'name'              =>  $user->name, 'email'=>$user->email,'phone' => $user->phone,
            'status'            =>  $user->active,
            'phone_verified_at' =>  $user->phone_verified_at,
            'created_at'        =>  date('Y-m-d H:i A', strtotime($user->created_at)),
            'image' => $userData->image,
            'national_id' => $userData->national_id,
            'national_id_image_f' => $userData->national_id_image_f,
            'national_id_image_b' => $userData->national_id_image_b,
            'track_type' => $userData->track_type,
            'driving_license_number' => $userData->driving_license_number,
            'driving_license_image_f' => $userData->driving_license_image_f,
            'driving_license_image_b' => $userData->driving_license_image_b,
            'track_license_number' => $userData->track_license_number,
            'track_license_image_f' => $userData->track_license_image_f,
            'track_license_image_b' => $userData->track_license_image_b,
            'track_image_f'         => $userData->track_image_f,
            'track_image_b'         => $userData->track_image_b,
            'track_image_s'         => $userData->track_image_s,
            'track_number' => $userData->track_number,
            'company_id' => $userData->company_id,
            'location'=>$userData->location,
            'longitude' => $userData->longitude,'latitude'=>$userData->latitude, 'type' => $userData->type,
        ];
        DB::commit();

        return $this->sendResponse($success, 'User register successfully.');
    }
    public function registerDriverCompany(Request $request)
    {
        /*add by mohammed*/
        $userphone = User::where('phone', $request->phone)->where('phone_verified_at',null)->first();
        if ($userphone) {
            $userDataPhone = UserData::where('user_id', $userphone->id)->first();
            if ($userDataPhone) {
                $userDataPhone->delete();
            }
            $userphone->delete();
        }
        $useremail = User::where('email', $request->email)->where('email',!null)->where('phone_verified_at',null)->first();
        if ($useremail){
            $userDataEmail = UserData::where('user_id', $useremail->id)->get()->first();
            if ($userDataEmail){
                $userDataEmail->delete();
            }
            $useremail->delete();
        }
        /*end*/
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email'=>'nullable|email|unique:users,email',
            'phone' => 'required|string|max:30|unique:users,phone',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'commercial_record' => 'required|string|max:30|unique:user_data,commercial_record',
            'tax_card' => 'required|string|max:30|unique:user_data,tax_card',
            'location'=>'nullable|string',
            'longitude' => 'nullable',
            'latitude'=>'nullable',
        ],[
            'phone.unique' => trans("validation.Account Already Exists"),
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

        $input['password'] = bcrypt($input['password']);
        DB::beginTransaction();
        $user = User::create([
            'name' => $input['name'],
            'email'=>$input['email'],
            'phone' =>  $input['phone'],
            'password' =>  $input['password'],
            'user_type' =>  'service_provider',
            'type' =>  'driverCompany',
            'active' => 0, // add by mohammed  to make the driver acount not active when reigster

        ]);
        $userData = UserData::create([
            'commercial_record'     =>      $request->commercial_record,
            'tax_card'              =>      $request->tax_card,
            'location'              =>      $request->location,
            'longitude'             =>      $request->longitude,
            'latitude'              =>      $request->latitude,
            'phone'                         =>  $user->phone,
            'type'                          =>  'driverCompany',
            'user_id'                       =>  $user->id,
        ]);
        $user1 = User::with(['userData', 'bank'])->find($user->id);

        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $userInfo =
            [
                'id'                        =>      $user1->id,
                'name'                      =>      $user1->name,
                'email'                     =>      $user1->email,
                'phone'                     =>      $user1->phone,
                'status'                    =>      $user1->active,
                'phone_verified_at'         =>      $user->phone_verified_at,
                'created_at'                =>      date('Y-m-d H:i A', strtotime($user->created_at)),
                'image'                     =>      $user1->userData->image,
                'commercial_record'         =>      $user1->userData->commercial_record,
                'commercial_record_image_f' =>      $user1->userData->commercial_record_image_f,
                'commercial_record_image_b' =>      $user1->userData->commercial_record_image_b,
                'location'                  =>      $user1->userData->location,
                'longitude'                      =>      $user1->userData->longitude,
                'latitude'                      =>      $user1->userData->latitude,
                'tax_card'                  =>      $user1->userData->tax_card,
                'tax_card_image_f'          =>      $user1->userData->tax_card_image_f,
                'tax_card_image_b'          =>      $user1->userData->tax_card_image_b,
            ];
        if (!empty($user->bank)) {
            $userInfo['bank_name']   =      $user1->bank->bank_name;
            $userInfo['branch_name']                   =      $user1->bank->branch_name;
            $userInfo['account_holder_name']           =      $user1->bank->account_holder_name;
            $userInfo['account_number']                =      $user1->bank->account_number;
            $userInfo['soft_code']                     =      $user1->bank->soft_code;
            $userInfo['iban']                          =      $user1->bank->iban;
        }
        $success['user'] = $userInfo;
        $data = [
            'title' => 'the_register',
            'body' => 'register_body',
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
        $otp_url = 'To verify phone via otp , required send phone number in body, use post method : ' . env('APP_URL') . 'api/auth/sendOtp';
        $success['otp_url'] = $otp_url;
        return $this->sendResponse($success, 'User register successfully.');
    }
    public function registerDriverCompanyInfo(Request $request,$id)
    {
        $user = User::with('userData')->find($id);
        if (!$user) {
            $msgs=['Shipping company not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
        $validator = Validator::make($request->all(), [
            'commercial_record'   =>  ['required', Rule::unique('user_data')->ignore($user->id,'user_id'),],
            'commercial_record' => 'string|max:255',
            'tax_card'   =>  ['required', Rule::unique('user_data')->ignore($user->id,'user_id'),],
            'tax_card' => 'string|max:255',
            'location'=>'nullable|string',
            'longitude' => 'nullable',
            'latitude'=>'nullable',
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
        $input = $request->all();
        $input['image'] = 'uploads/users/default.png';

        DB::beginTransaction();
        $userData = UserData::where('user_id',$user->id)->first();
        $userData->update([
            'commercial_record' =>  $input['commercial_record'],
            'tax_card' =>  $input['tax_card'],
            'location'                =>  $input['location'],
            'longitude'                =>  $input['longitude'],
            'latitude'              =>  $input['latitude'],
            'revision' => 1   // add by mohammed

        ]);
        $userBank=BankInfo::where('user_id',$user->id)->first();

        if ($request->has('bank_name') && $request->has('branch_name') &&  $request->has('account_holder_name') && $request->has('account_number')) {
            if($userBank){
                $userBank ->update([
                    'user_id'                   =>      $user->id,
                    'bank_name'                 =>      $input['bank_name'],
                    'branch_name'               =>      $input['branch_name'],
                    'account_holder_name'       =>      $input['account_holder_name'],
                    'account_number'            =>      $input['account_number'],
                    'soft_code'                 =>      $input['soft_code'],
                    'iban'                      =>      $input['iban'],
                ]);
            }
            else{
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

        $user1 = User::with(['userData', 'bank'])->find($user->id);
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $userInfo = [
            'id'                        =>      $user1->id,
            'name'                      =>      $user1->name,
            'email'                     =>      $user1->email,
            'phone'                     =>      $user1->phone,
            'status'                    =>      $user1->active,
            'phone_verified_at'         =>      $user->phone_verified_at,
            'created_at'                =>      date('Y-m-d H:i A', strtotime($user->created_at)),
            'image'                     =>      $user1->userData->image,
            'commercial_record'         =>      $user1->userData->commercial_record,
            'commercial_record_image_f' =>      $user1->userData->commercial_record_image_f,
            'commercial_record_image_b' =>      $user1->userData->commercial_record_image_b,
            'tax_card'                  =>      $user1->userData->tax_card,
            'tax_card_image_f'          =>      $user1->userData->tax_card_image_f,
            'tax_card_image_b'          =>      $user1->userData->tax_card_image_b,
            'location'                  =>      $user1->userData->location,
            'longitude'                      =>      $user1->userData->longitude,
            'latitude'                      =>      $user1->userData->latitude,
        ];
        if (!empty($user->bank)) {
            $userInfo['bank_name']   =      $user1->bank->bank_name;
            $userInfo['branch_name']                   =      $user1->bank->branch_name;
            $userInfo['account_holder_name']           =      $user1->bank->account_holder_name;
            $userInfo['account_number']                =      $user1->bank->account_number;
            $userInfo['soft_code']                     =      $user1->bank->soft_code;
            $userInfo['iban']                          =      $user1->bank->iban;
        }
        $success['user'] = $userInfo;
        DB::commit();

        return $this->sendResponse($success, 'Driver company information added successfully.');
    }
    public function registerDriverCompanyImage(Request $request,$id)
    {
        $user = User::with('userData')->find($id);
        if (!$user) {
            $msgs=['Shipping Company not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
        $validator = Validator::make($request->all(), [
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
        $userData = UserData::where('user_id',$user->id)->first(); // add by mohammed
        if ($userData->image == null){ // add if by mohammed
            $input['image'] = 'uploads/users/default.png';
        }
        if ($request->image) {
            Image::make($request->image)
                ->save(public_path('uploads/users/' . $request->image->hashName()));
            $input['image'] = 'uploads/users/' . $request->image->hashName();
        }elseif (!$request->image && $userData->image != null){  // add elseif by mohammed
            $input['image'] = $userData->image;
        }
        $input['commercial_record_image_f'] = $userData->commercial_record_image_f;  // edit by mohammed form $input['commercial_record_image_f'] = null
        if ($request->commercial_record_image_f) {
            Image::make($request->commercial_record_image_f)
                ->save(public_path('uploads/commercial_records/' . $request->commercial_record_image_f->hashName()));
            $input['commercial_record_image_f'] = 'uploads/commercial_records/' . $request->commercial_record_image_f->hashName();
        }
        $input['commercial_record_image_b'] = $userData->commercial_record_image_b; // edit by mohammed form $input['commercial_record_image_b'] = null
        if ($request->commercial_record_image_b) {
            Image::make($request->commercial_record_image_b)
                ->save(public_path('uploads/commercial_records/' . $request->commercial_record_image_b->hashName()));
            $input['commercial_record_image_b'] = 'uploads/commercial_records/' . $request->commercial_record_image_b->hashName();
        }
        $input['tax_card_image_f'] = $userData->tax_card_image_f;  // edit by mohammed form $input['tax_card_image_f'] = null
        if ($request->tax_card_image_f) {
            Image::make($request->tax_card_image_f)
                ->save(public_path('uploads/tax_cards/' . $request->tax_card_image_f->hashName()));
            $input['tax_card_image_f'] = 'uploads/tax_cards/' . $request->tax_card_image_f->hashName();
        }
        $input['tax_card_image_b'] = $userData->tax_card_image_b;      // edit by mohammed form $input['tax_card_image_b'] = null
        if ($request->tax_card_image_b) {
            Image::make($request->tax_card_image_b)
                ->save(public_path('uploads/tax_cards/' . $request->tax_card_image_b->hashName()));
            $input['tax_card_image_b'] = 'uploads/tax_cards/' . $request->tax_card_image_b->hashName();
        }

        DB::beginTransaction();
        /*$userData = UserData::where('user_id',$user->id)->first();*/  // commit by mohammed
        $userData->update([
            'image'                             =>  $input['image'],
            'commercial_record_image_f'         =>  $input['commercial_record_image_f'],
            'commercial_record_image_b'         =>  $input['commercial_record_image_b'],
            'tax_card_image_f'                  =>  $input['tax_card_image_f'],
            'tax_card_image_b'                  =>  $input['tax_card_image_b'],
        ]);
        //$success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['user'] = [
            'id' =>  $user->id,
            'name'              =>  $user->name,'email'=>$user->email, 'phone' => $user->phone,
            'status'            =>  $user->active,
            'phone_verified_at' =>  $user->phone_verified_at,
            'created_at'        =>  date('Y-m-d H:i A', strtotime($user->created_at)),
            'image' => $userData->image,
            'commercial_record' => $userData->commercial_record,
            'commercial_record_image_f' => $userData->commercial_record_image_f,
            'commercial_record_image_b' => $userData->commercial_record_image_b,
            'tax_card' => $userData->tax_card,
            'tax_card_image_f' => $userData->tax_card_image_f,
            'tax_card_image_b' => $userData->tax_card_image_b,
            'location'=>$userData->location,
            'longitude' => $userData->longitude,'latitude'=>$userData->latitude, 'type' => $userData->type,
        ];
        DB::commit();

        return $this->sendResponse($success, 'Driver company images uploaded successfully.');
    }
    public function username()
    {
        return 'phone';
    }
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:255|exists:users,phone',
            'password' => 'required'
        ],[
            'phone.exists' => trans("validation.Incorrect Login Details, Please try again."),
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $msgs=[];
            foreach ($errors->all() as  $ind=>$message) {
                array_push($msgs, $message);
            }
            return $this->sendError('Validation Error.', $msgs);
        }

        if (Auth::attempt(['phone' => $request->phone, 'password' => $request->password])) {
            $user = Auth::user();
            // if ($user->type != 'user') was added by Muhammad AlFarra.
            if ($user->type != 'user'){
                // if ($user->userData->revision == 1) {
                    $user_data = UserData::where('user_id',$user->id)->first();
                    $success['token'] =  $user->createToken('MyApp')->accessToken;
    
                    $success['user'] = [
                        'id' =>  $user->id,
                        'name' => $user->name,
                        'email'=>$user->email,
                        'phone'=>$user->phone,
                        'type'=>$user->type,
                        'image'                      =>      $user->userData->image,
                        'location'          =>      $user->userData->location,
                        'longitude'                      =>      $user->userData->longitude,
                        'latitude'                      =>      $user->userData->latitude,
                        'active'=>$user->active,
                        'active_text'=> $user->getActiveType(),
                        'revision' => $user_data->revision,
                        'revision_text' => $user_data->getRevision(),
                        'status' => $user_data->status,
                        'vip' => $user_data->vip,
                        'phone_verified_at'=>$user->phone_verified_at,
                        'created_at'=>date('Y-m-d H:i A', strtotime($user->created_at)),
                    ];
    
                    return $this->sendResponse($success, 'User login successfully.');
    
              /*  }
                else{
                    return $this->sendError('Validation Error.',trans('Account under review You cannot log in until your application has been reviewed and approved'));
                } */ 
            }else{
                $user_data = UserData::where('user_id',$user->id)->first();
                $success['token'] =  $user->createToken('MyApp')->accessToken;

                $success['user'] = [
                    'id' =>  $user->id,
                    'name' => $user->name,
                    'email'=>$user->email,
                    'phone'=>$user->phone,
                    'type'=>$user->type,
                    'image'                      =>      $user->userData->image,
                    'location'          =>      $user->userData->location,
                    'longitude'                      =>      $user->userData->longitude,
                    'latitude'                      =>      $user->userData->latitude,
                    'active'=>$user->active,
                    'active_text'=> $user->getActiveType(),
                    'revision' => $user_data->revision,
                    'revision_text' => $user_data->getRevision(),
                    'status' => $user_data->status,
                    'vip' => $user_data->vip,
                    'phone_verified_at'=>$user->phone_verified_at,
                    'created_at'=>date('Y-m-d H:i A', strtotime($user->created_at)),
                ];

                return $this->sendResponse($success, 'User login successfully.');
            }

        }
        else {
            $msgs=['Password is Incorrect'];   /* change by mohammed form Password not correct*/
            return $this->sendError('Validation Error.',$msgs);
        }
    }
    public function userInfo()
    {
        $user = auth()->user();
        $success['user'] =  $user;
        return $this->sendResponse($success, 'User information.');
    }
    public function userEdit(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required',
            'c_password' => 'required|same:password',
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
        $user->update($input);
        $success['token'] =  $user->createToken('MyApp')->accessToken;

        $success['user'] = [
            'name' => $user->name,
            'id' =>  $user->id
        ];
        return $this->sendResponse($success, 'User updated success.');
    }
    public function sendOtp(Request $request)
    {
        // $request->validate(['phone' => 'required|exists:users,phone']);
        $user = auth()->user();

        // Delete all old code that user send before.
        ResetCodePassword::where('phone', $user->phone)->delete();
        // Generate random code
        $data['phone'] = $user->phone;
        $data['code'] = mt_rand(100000, 999999);
        $data['for'] = 'Reset Password';
        // Create a new code
        $codeData = ResetCodePassword::create($data);
        $msgData = [
            'sender_id' => $user->id,
            'receiver_id' => $user->id,
            'message' => $codeData->code,
            'to'=>$user->phone,
            'type' => 'Sms for otp',
        ];
        // Send to one number
        $response = InfobipSms::send($user->phone, $codeData->code);
        $success['message'] = 'Otp Verification';
        
        if ($response[0] != 200) {
            $msgData['status'] = 'wait';
            $success=$response;
            return $this->sendError($success, 'Otp not sended , Plase try angin.');
        }
//        $msgData['status'] = 'complete';

        $msgData['status'] = $response[1]->messages[0]->status->groupName;
        Message::create($msgData);
        $success['code'] = $codeData->code;
        $success['expire_at']=date('Y-m-d H:i A', strtotime($codeData->created_at->addHour()));
        $success['res'] = $response[1]->messages[0]->status->groupName;
        return $this->sendResponse($success, 'Otp Code ' . $response[1]->messages[0]->status->groupName);
    }
    public function verifyOtp(Request $request)
    {
        $request->validate([]);
        $user = auth()->user();
        try {
            $passwordReset = ResetCodePassword::where('phone',auth()->user()->phone)->firstWhere('code', $request->code);

            if(!$passwordReset){
                return response(['success'=>false,'data'=>'Code not found','message'=>'code is not valid , or not exists , please try generate code again']);
            }
            if ($passwordReset->isExpire()) {

                $msgs='Code if expire at '. date('Y-m-d H:i A', strtotime($passwordReset->created_at->addHour()));
                return $this->sendError('Validation Error.',$msgs);
            }
            $user->update(['phone_verified_at' => now()]);

            $passwordReset->delete();
            $success['user'] =  [
                'id' => $user->id, 'name' => $user->name, 'phone' => $user->phone,
                'status'=>$user->active,
                'phone_verified_at'=>date('Y-m-d H:i A', strtotime($user->phone_verified_at)),
                'created_at'=>date('Y-m-d H:i A', strtotime($user->created_at))
            ];
            return $this->sendResponse($success, 'User phone verification success.');
        }catch (Exception $e) {
            return $this->sendError('Validation Error.','OTP incorrect Please try again');
        }
    }
public function registerDriverFromCompany(Request $request, $id)
{
    $company = User::find($id);
    if (!$company || $company->type !== 'driverCompany') {
        return $this->sendError('Company not found or invalid type.', []);
    }

    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|unique:users,email',
        'phone' => 'required|string|max:30|unique:users,phone',
        'password' => 'required',
        'c_password' => 'required|same:password',
        'commercial_record' => 'nullable|string|max:30',
        'tax_card' => 'nullable|string|max:30',
        'location' => 'nullable|string',
        'bank_name' => 'nullable|string',
        'branch_name' => 'nullable|string',
        'account_holder_name' => 'nullable|string',
        'account_number' => 'nullable|string',
        'soft_code' => 'nullable|string',
        'iban' => 'nullable|string',
        'image' => 'nullable|image|max:10240',
        'commercial_record_image_f' => 'nullable|image|max:10240',
        'commercial_record_image_b' => 'nullable|image|max:10240',
        'tax_card_image_f' => 'nullable|image|max:10240',
        'tax_card_image_b' => 'nullable|image|max:10240',
        'active' => 'nullable|boolean',
    ]);

    if ($validator->fails()) {
        return $this->sendError('Validation Error.', $validator->errors()->all());
    }

    DB::beginTransaction();

    $input = $request->all();
    $input['password'] = bcrypt($input['password']);

    $driver = User::create([
        'name' => $input['name'],
        'email' => $input['email'] ?? null,
        'phone' => $input['phone'],
        'password' => $input['password'],
        'user_type' => 'service_provider',
        'type' => 'driver',
        'active' => $request->active ?? 0,
    ]);

    $userData = new UserData([
        'type' => 'driver',
        'phone' => $driver->phone,
        'user_id' => $driver->id,
        'company_id' => $company->id,
        'commercial_record' => $input['commercial_record'] ?? null,
        'tax_card' => $input['tax_card'] ?? null,
        'location' => $input['location'] ?? null,
    ]);

    foreach (['image', 'commercial_record_image_f', 'commercial_record_image_b', 'tax_card_image_f', 'tax_card_image_b'] as $field) {
        if ($request->hasFile($field)) {
            $path = 'uploads/drivers/';
            $filename = $request->file($field)->hashName();
            Image::make($request->file($field))->save(public_path($path . $filename));
            $userData->$field = $path . $filename;
        }
    }

    $userData->save();

    if ($request->has('bank_name')) {
        BankInfo::create([
            'user_id' => $driver->id,
            'bank_name' => $input['bank_name'],
            'branch_name' => $input['branch_name'],
            'account_holder_name' => $input['account_holder_name'],
            'account_number' => $input['account_number'],
            'soft_code' => $input['soft_code'],
            'iban' => $input['iban'],
        ]);
    }

    DB::commit();

    $success['user'] = [
        'id' => $driver->id,
        'name' => $driver->name,
        'email' => $driver->email,
        'phone' => $driver->phone,
        'type' => $driver->type,
        'active' => $driver->active,
        'image' => $userData->image,
        'commercial_record' => $userData->commercial_record,
        'tax_card' => $userData->tax_card,
        'location' => $userData->location,
        'company_id' => $userData->company_id,
    ];

    return $this->sendResponse($success, 'Driver registered by company successfully.');
}


private function saveImage($file, $folder)
{
    if (!$file) return null;

    $path = 'uploads/' . $folder . '/' . $file->hashName();
    \Intervention\Image\Facades\Image::make($file)->save(public_path($path));
    return $path;
}

}
