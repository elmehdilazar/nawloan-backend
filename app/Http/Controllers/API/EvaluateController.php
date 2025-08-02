<?php

namespace App\Http\Controllers\API;

use App\Models\Evaluate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EvaluateController extends BaseController
{
    public function index()
    {
        $evaluates = Evaluate::active()->with(['user', 'order', 'user2'])->get();
        $success['count'] =  $evaluates->count();
        $success['evaluates'] =  $evaluates;
        return $this->sendResponse($success, 'Evaluates information.');
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'comment'       =>      'nullable|string',
            'rate'          =>      'required|numeric',
            'user2_id'      =>      'required|exists:users,id',
            'order_id'      =>      'required|exists:orders,id',
            'user_id'       =>      'required|exists:users,id', '
            notes'          =>      'nullable|string'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $msgs = [];
            foreach ($errors->all() as  $ind => $message) {
                array_push($msgs, $message);
            }
            return $this->sendError('Validation Error.', $msgs);
        }
        $evaluate   =   Evaluate::create([
            'comment'       =>      $request->comment,
            'comment_replay'=>      $request->comment_replay,
            'rate'          =>      $request->rate,
            'user_id'      =>      $request->user_id,
            'order_id'      =>      $request->order_id,
            'user2_id'       =>      $request->user2_id,
            'notes'          =>      $request->notes,
        ]);
        $success['Evaluate'] =  $evaluate;
        return $this->sendResponse($success, 'Evaluate created successfully.');
    }
    public function commentReplay(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'comment_replay'       =>      'required|string',
            'user2_id'              =>  'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $msgs = [];
            foreach ($errors->all() as  $ind => $message) {
                array_push($msgs, $message);
            }
            return $this->sendError('Validation Error.', $msgs);
        }
        $evaluate = Evaluate::where('user2_id', $request->user2_id)->find($id);
        if (!$evaluate) {
			$msgs=['Evaluate not exists'];
            return $this->sendError('Data not found.',$msgs);
        }
        $evaluate->update([
            'comment_replay' =>      $request->comment_replay,
        ]);
        $success['Evaluate'] =  $evaluate;
        return $this->sendResponse($success, 'Evaluate replay successfully.');
    }

/*     public function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'comment'       =>      'nullable|string',
            'rate'          =>      'required|numeric',
            'user2_id'      =>      'required|exists:users,id',
            'order_id'      =>      'required|exists:orders,id',
            'user_id'       =>      'required|exists:users,id', '
            nodes'          =>      'nullable|string'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $evaluate = Evaluate::where('user_id', $request->user_id)->find($id);
        if (!$evaluate) {
            return $this->sendError('Data not found', 'Evaluate information not exists');
        }
        $evaluate->update([
            'comment'       =>      $request->comment,
            'comment_replay' =>      $request->comment_replay,
            'rate'          =>      $request->rate,
            'user2_id'      =>      $request->user_id,
            'order_id'      =>      $request->order_id,
            'user_id'       =>      $request->user2_id, '
            nodes'          =>      $request->notes
        ]);
        $success['Evaluate'] =  $evaluate;
        return $this->sendResponse($success, 'Evaluate created successfully.');
    } */
    public function showByOrder($id)
    {
        //$evaluate = Evaluate::active()->with(['user','order','user2'])->where('order_id',$id)->get();
          $evaluate = Evaluate::where('order_id',$id)->with(['user','order','user2'])->get();
      
        $success['Evaluate'] =  $evaluate;
        return $this->sendResponse($success, 'Evaluate information.');
    }
    public function showByUser($id)
    {
        $evaluate = Evaluate::active()->with(['user', 'order', 'user2'])->where('user_id', $id)->get();
          //$evaluate = Evaluate::where('order_id',$id)->with(['user','order','user2'])->get();
        
        $success['Evaluate'] =  $evaluate;
        return $this->sendResponse($success, 'Evaluate information.');
    }
    public function showByUser2($id)
    {
        $evaluate = Evaluate::active()->with(['user2', 'order'])->with(['user' => function ($query) {
            $query->with('userData');
        }])->where('user2_id', $id)->get();
        $success['Evaluate'] =  $evaluate;
        return $this->sendResponse($success, 'Evaluate information.');
    }
    public function checkIfRatingExists(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'user2_id' => 'required|exists:users,id',
        'order_id' => 'required|exists:orders,id',
    ]);

    $ratingExists = Evaluate::where('user_id', $request->user_id)
        ->where('user2_id', $request->user2_id)
        ->where('order_id', $request->order_id)
        ->exists();

    if ($ratingExists) {
        return response()->json([
            'message' => 'Rating already exists.',
            'exists' => true
        ]);
    }

    return response()->json([
        'message' => 'No rating found.',
        'exists' => false
    ]);
}

    
    
}
