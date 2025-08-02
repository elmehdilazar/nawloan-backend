<?php

namespace App\Http\Controllers\API;

use App\Events\MessageDelivered;
use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Notifications\FcmPushNotification;



class ChatController extends BaseController
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
    public function getByOrderId($id){
        $rooms=ChatRoom::where('active',1)->latest()->with(['users','messages'])->with('users',function($xm){
                    $xm->with('userData')->get();
            
        })->with('messages',function($m){
            $m->with(['user','userData'])->get();
        })->where('order_id',$id)->get();
        $success['count'] =  $rooms->count();
        $success['rooms'] =  $rooms;
        return $this->sendResponse($success, 'Chat rooms for order .');
    }
    public function getByUserId($id){
        $rooms=ChatRoom::where('active',1)->latest()->with(['users','messages'])->with('users',function($xm){
                    $xm->with('userData')->get();
            
        })->with('messages',function($m) use($id){
            $m->with(['user','userData'])->get();
        })->whereHas('users',function ($q)use ($id){
                $q->where('user_id',$id);
            })->get();
        $success['count'] =  $rooms->count();
        $success['rooms'] =  $rooms;
        return $this->sendResponse($success, 'Chat rooms service seeker.');
    }
    public function getNewMessagesId(Request $request,$id){
        $messages=ChatMessage::where('chat_room_id',$id)->get();
        if(!empty($request->last_message_id)){
        $msgs=[];
        $count=0;
        foreach($messages as $msg){
            if($msg->id >= $request->last_message_id + 1){
                array_push($msgs,$msg);
                $count++;
            }
        }
        $success['count'] =  $count;
        $success['last_message_id']=$request->last_message_id;
        $success['messages'] =  $msgs;
        }
        else
        {
            $success['count'] =  $messages->count();
            $success['messages'] =  $messages;
        }
        return $this->sendResponse($success, 'New Messages in room.');
    }
    public function  store(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:chat_rooms,id',
            'body'=>'required',
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
        $user=User::find($request->user_id);
        if($user->type == 'emp' || $user->type == 'admin' || $user->type== 'superadministrator'){
            $rooms=ChatRoom::with('users')->find($request->room_id);
            $user_id=0;
            foreach($rooms->users as $usr){
                if($user->id == $usr->id)
                {
                Log::info('exist : '.$usr);
                    $user_id ++;
                }
            }
            if($user_id==0){

                Log::info('add : ' . $user);
             $room=  ChatRoom::find( $request->room_id);
             $room-> join($user->id);
            }
        
        }
        $message = ChatMessage::create([
            'body'      =>      $request->body,
            'user_id'   =>      $request->user_id,
            'chat_room_id'   =>      $request->room_id
        ]);
        broadcast(new MessageDelivered($message, $message->chat_room_id))->toOthers();
        DB::commit();
       $success['message'] =  $message;
       $title="New Message";
                $success['users_Fcm'] =  $request->receiver_ids;

        if (!empty($request->receiver_ids) && is_array($request->receiver_ids)) {
    foreach ($request->receiver_ids as $fcm_token) {
        Notification::send($user, new FcmPushNotification($title, $request->body, [$fcm_token]));
    }
} else {
    Log::error("receiver_ids is missing or not an array", ['receiver_ids' => $request->receiver_ids]);
}

        return $this->sendResponse($success, 'message sended.');
    }
}