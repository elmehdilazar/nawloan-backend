<?php

namespace App\Http\Controllers\API;
use App\Models\SupportCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupportCenterController extends BaseController
{
    public function getByUserId($id)
    {
        $messages = SupportCenter::where('user_id',$id)->get();
        $success['count'] =  $messages->count();
        $success['messages'] =  $messages;
        return $this->sendResponse($success, 'Support center messages.');
    }
    public function show($id)
    {
        $message = SupportCenter::find($id);
        $success['message'] =  $message;
        return $this->sendResponse($success, 'Support center message.');
    }
    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'title' => 'required|string',
    //         'message' => 'required|string',
    //         'desc' => 'nullable|string',
    //         'notes' => 'nullable|string',
    //         'user_id' => 'required|exists:users,id',
    //     ]);

    //     if ($validator->fails()) {
    //         $errors = $validator->errors();
    //         $msgs = [];
    //         foreach ($errors->all() as  $ind => $message) {
    //             array_push($msgs, $message);
    //         }
    //         return $this->sendError('Validation Error.', $msgs);
    //     }
    //     $supportCenter = SupportCenter::create([
    //         'user_id' => $request->user_id,
    //         'title' => $request->title,
    //         'message' => $request->message,
    //         'desc' => $request->desc,
    //         'notes' => $request->notes,
    //     ]);
    //     $success['supportCenter'] =  $supportCenter;
    //     return $this->sendResponse($success, 'Support center message created successfully.');
    // }
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'phone_number' => 'required|string|max:10',
        'phone_code' => 'required|string|max:4',
        'title' => 'required|string',
        'message' => 'required|string',
        'desc' => 'nullable|string',
        'notes' => 'nullable|string',
        'user_id' => 'required|exists:users,id',
    ]);

    if ($validator->fails()) {
        $errors = $validator->errors();
        $msgs = [];
        foreach ($errors->all() as $message) {
            $msgs[] = $message;
        }
        return $this->sendError('Validation Error.', $msgs);
    }

    $supportCenter = SupportCenter::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone_number' => $request->phone_number,
        'phone_code' => $request->phone_code,
        'user_id' => $request->user_id,
        'title' => $request->title,
        'message' => $request->message,
        'desc' => $request->desc,
        'notes' => $request->notes,
    ]);

    $success['supportCenter'] = $supportCenter;
    return $this->sendResponse($success, 'Support center message created successfully.');
}

}
