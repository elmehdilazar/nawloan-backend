<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Policy;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    public function index(Request $request){
        
        $policies  = Policy::withTrashed();
        $rowsNumber = $policies->count();
        return response()->json($policies);
    }

}

