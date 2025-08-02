<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Car;

class CarController extends BaseController
{
    public function index()
    {
        $cars = Car::get();
        $success['count'] =  $cars->count();
        $success['cars'] =  $cars;
        return $this->sendResponse($success, 'Cars information.');
    }
    public function show($id)
    {
        $car = Car::find($id);
        $success['car'] =  $car;
        return $this->sendResponse($success, 'Car information.');
    }
}
