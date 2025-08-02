<?php

namespace App\Http\Controllers\API;
use App\Models\CodeScretModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CodeScretController extends BaseController
{
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'code' => 'required|string',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $msgs = [];
            foreach ($errors->all() as  $ind => $message) {
                array_push($msgs, $message);
            }
            return $this->sendError('Validation Error.', $msgs);
        }
        $codeScretModel = CodeScretModel::create([
            'user_id' => $request->user_id,
            'json' =>json_encode($request->json),
            'code' => $request->code
        ]);
        $success['codeScretModel'] =  $codeScretModel;
        return $this->sendResponse($success, 'Generate Secret Code Successfully.');
    }
}
