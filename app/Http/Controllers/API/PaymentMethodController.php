<?php

namespace App\Http\Controllers\API;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends BaseController
{
    public function index()
    {
        $paymentMethods = PaymentMethod::select('id','name','type')->where('type','payment')->active()->get();
        $success['count'] =  $paymentMethods->count();
        $success['paymentMethods'] =  $paymentMethods;
        return $this->sendResponse($success, 'Payment Methods information.');
    }
    public function show($id)
    {
        $paymentMethod = PaymentMethod::find($id);
        $success['paymentMethod'] =  $paymentMethod;
        return $this->sendResponse($success, 'Payment Method information.');
    }
}
