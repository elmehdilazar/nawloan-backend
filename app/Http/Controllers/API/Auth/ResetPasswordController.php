<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\ResetCodePassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends BaseController
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'=>'required|string|exists:reset_code_passwords,phone',
            'code' => 'required|string|exists:reset_code_passwords,code',
            'password' => 'required|string',
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

        $passwordReset = ResetCodePassword::where('phone',$request->phone)->firstWhere('code', $request->code);

        if(!$passwordReset){
            return response(['success'=>false,'Data'=>'Code Not Valid','message'=>'code is not valid , or not exists , please try generate code again']);
        }
        if ($passwordReset->isExpire()) {
            return response(['success'=>false,'Data'=>'Code expire','message'=> 'code is expire at '. $passwordReset->created_at->addHour()]);
        }

        $user = User::where('phone', $passwordReset->phone)->get()->first();

        $user->update(['password'=>bcrypt($request->password)]);

        $passwordReset->delete();

        return response(['success'=>true,'message' =>'password has been successfully reset']);
    }
}
