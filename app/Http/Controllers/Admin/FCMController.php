<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class FCMController extends Controller
{
    //
    public function index(Request $request)
    {
        $input = $request->all();

        $fcm_token = $input['fcm_token'];
        $user_id = $input['user_id'];


        $user = User::findOrFail($user_id);

        $user->fcm_token = $fcm_token;
        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'User token updated successfully.'
        ]);
    }
}
