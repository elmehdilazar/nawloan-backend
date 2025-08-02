<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Mail\SendCodeResetPassword;
use App\Models\Message;
use App\Models\ResetCodePassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Pnlinh\InfobipSms\Facades\InfobipSms;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends BaseController
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|exists:users,phone',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $msgs = [];
            foreach ($errors->all() as  $ind => $message) {
                array_push($msgs, $message);
            }
            return $this->sendError('Validation Error.', $msgs);
        }
        // Delete all old code that user send before.
        ResetCodePassword::where('phone', $request->phone)->delete();

        // Generate random code
        $data['code'] = mt_rand(100000, 999999);

        $data['phone'] = $request->phone;
        $data['for']='Reset Password';
        // Create a new code
        $codeData = ResetCodePassword::create($data);
        $user=User::where('phone',$request->phone)->get()->first();
        $msgData = [
            'sender_id' => $user->id,
            'receiver_id' => $user->id,
            'message' => $codeData->code,
            'to' => $user->phone,
            'type' => 'Sms for otp',
        ];
        // Send email to user
       // Mail::to($request->email)->send(new SendCodeResetPassword($codeData->code));

        // Send to one number
        $response = InfobipSms::send($request->phone, $codeData->code);
            $success['message']="Otp Verification";
    if($response[0]!=200)
        {
            $msgData['status'] = 'wait';
        return $this->sendResponse($success, 'Otp code not send,Plase try angin. ');
        }
        $msgData['status'] = $response[1]->messages[0]->status->groupName;
        Message::create($msgData);
        $success['expire_at']=date('Y-m-d H:i A', strtotime($codeData->created_at->addHour()));
        $success['code'] = $codeData->code;
        $success['res'] = $response[1]->messages[0]->status->groupName;
        return $this->sendResponse($success, 'Otp code sended successfully.');
    }
}
