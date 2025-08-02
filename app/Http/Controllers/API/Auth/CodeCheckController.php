<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\ResetCodePassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CodeCheckController extends BaseController
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|exists:reset_code_passwords,code',
            'phone'=>'required|string|exists:users,phone'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $msgs = [];
            foreach ($errors->all() as  $ind => $message) {
                array_push($msgs, $message);
            }
            return $this->sendError('Validation Error.', $msgs);
        }
        // find the code
        $passwordReset = ResetCodePassword::where('phone',$request->phone)->where('code', $request->code)->get()->first();

        if (!$passwordReset) {
            return response(['success' => false, 'message' => 'code is not valid , or not exists , please try generate code again']);
        }
        // check if it does not expired: the time is one hour
        if ($passwordReset->created_at > now()->addHour()) {
            $exp_at=$passwordReset->created_at->addHour();
           $passwordReset->delete();
            return response(['success'=>false,'message' => 'code is not valiid','expireat :'.date('Y-m-d H:i A', strtotime($exp_at))], 422);
        }

        return response(['success'=>true,'message' => 'code is valid'], 200);
    }
}
