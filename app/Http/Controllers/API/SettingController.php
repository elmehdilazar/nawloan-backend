<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

class SettingController extends BaseController
{
    public function index()
    {
        $setting=[
            'fine' => Setting('fine'),
            'avg_fuel_consumption_per_10_km' => Setting('avg_fuel_consumption_per_10_km'),
            'vat' => Setting('vat'),
            'liter_price' => Setting('liter_price'),
            'diesel_cost_per_km' => Setting('diesel_cost_per_km'),
            'operating_costs' => Setting('operating_costs'),
            'expenses' => Setting('expenses'),
            'driver_commission' => Setting('driver_commission'),
            'company_commission' => Setting('company_commission'),
            'customer_fee' => Setting('customer_fee'),
            'company_fee' => Setting('company_fee'),
            'policy' => Setting('policy'),
        ];
        $success['Settings'] =  $setting;
        return $this->sendResponse($success, 'Settings information.');
    }
}
