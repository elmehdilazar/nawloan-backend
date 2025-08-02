<?php

namespace App\Http\Controllers\API;

use App\Models\ShipmentType;
use Illuminate\Http\Request;

class ShipmentTypeController extends BaseController
{
    public function index()
    {
        $shipmentTypes = ShipmentType::get();
        $success['count'] =  $shipmentTypes->count();
        $success['shipmentTypes'] =  $shipmentTypes;
        return $this->sendResponse($success, 'Shipment Types information.');
    }
    public function show($id)
    {
        $shipmentType = ShipmentType::find($id);
        $success['shipmentType'] =  $shipmentType;
        return $this->sendResponse($success, 'Shipment Type information.');
    }
}
