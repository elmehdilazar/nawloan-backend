<?php

namespace App\Http\Controllers\Admin;

use App\Events\MessageDelivered;
use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
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
    public function index(Request $request){
         $rooms = ChatRoom::when($request->search, function ($query) use ($request) {
        return $query->where('order_id', $request->search)
            ->orWhereHas('user', function ($q) use ($request) {
                return $q->where('name', $request->search)->get();
            })->get();
        })->where('active', 1)->latest()->with(['users', 'messages'])->with('messages', function ($m) {
            $m->with('user')->get();
        })->get();
        
        // Format created_at for each message using Carbon
        $rooms->each(function ($room) {
            $room->messages->each(function ($message) {
                $message->formatted_created_at = \Carbon\Carbon::parse($message->created_at)->diffForHumans();
            });
        });
        
        return view('admin.chat.index', ['rooms' => $rooms]);
    }
    
    public function  store(Request $request){
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
            'user_id'   =>      auth()->user()->id,
            'chat_room_id'   =>      $request->room_id
        ]);
        broadcast(new MessageDelivered($message, $message->chat_room_id))->toOthers();
        DB::commit();
    }
    
    public function roomsAjax(Request $request){
        if ($request->ajax()) {
            $rooms= ChatRoom::where('id',1)->get();
            return view('admin.chat.rooms', compact('rooms'))->render();
        }
      //  dd($request);
      //  return response()->json(['search'=>$request->search]);
    }
    
    public function messagesAjax(Request $request)
    {
        if ($request->ajax()) {
            $messages = ChatMessage::where('chat_room_id', $request->room_id)->get();
            return view('admin.chat.messages', compact('messages'))->render();
        }
    }
 }
