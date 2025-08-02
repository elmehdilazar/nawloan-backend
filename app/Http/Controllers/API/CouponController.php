<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Translation\Translator;
use App\Http\Requests\CouponRequest;
use App\Models\Coupon;
use App\Models\User;
use App\Notifications\LocalNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class CouponController extends BaseController
{
    public function index (Request $request){
        try {
            if($request->has('quick_search')){
                $coupons = $this->quickSearch($request) ;
                return $this->sendResponse($coupons, 'get all coupons success');

            }
            $coupons = Coupon::all();
            return response()->json($coupons);
            return $this->sendResponse($coupons, 'get all coupons success');

        } catch (\Excaption $th) {
            return response()->json('Error: '.$th,200);
        }
    }

    private function quickSearch(Request $request ){
        try {
            $coupons = Coupon::where('name','like','%'.$request->quick_search.'%')
            ->orWhere('code','like','%'.$request->quick_search.'%')
            ->orWhere('number_availabe','like','%'.$request->quick_search.'%')
            ->orWhere('discount','like','%'.$request->quick_search.'%')
            ->orWhere('apply_to','like','%'.$request->quick_search.'%')
            ->orWhere('start_date','like','%'.$request->quick_search.'%')
            ->orWhere('expiry_date','like','%'.$request->quick_search.'%')
            ->get();
            return $coupons;
        } catch (\Excaption $th) {
            return response()->json('Error: '.$th,200);
           }
    }

    public function store(CouponRequest $request){
        try {
            if ($request->type!='discount' && $request->type!='fixed') {
                return response()->json('the type field must contain discount or fixed ');
            }
            if ($request->apply_to!='enterprise' && $request->apply_to!='customer' && $request->apply_to!='all') {
                return response()->json('the apply to field must contain enterprise or customer or all ');
            }
        DB::beginTransaction();
        $coupon = $request->validated();
        $coupon['user_id'] = auth()->user()->id;
        Coupon::create($coupon);
         $data = [
             'title' =>'Add',
             'body' => "Add ",
             'target' => 'coupon',
             'link'  => route('admin.coupons.index', [ 'name' => $coupon['name']]),
             'target_id' => $coupon['name'],
             'sender' => auth()->user()->name,
             ];
         $this->sendNotification($data);
         DB::commit();
            return $this->sendResponse($coupon, 'Add new  coupons success');
        } catch (\Excaption $th) {
            return response()->json('Error: '.$th);
        }
    }

    public function update(CouponRequest $request , $id){
        try {
            $coupon = Coupon::withTrashed()->findOrFail($id);
            if (auth()->user()->id !=$coupon->user_id && auth()->user()->type != 'admin'  && auth()->user()->type !='superadministrator' && auth()->user()->type != 'emp') {
                return response()->json('action is denied');
            }
            if ($request->type!='percentage' && $request->type!='fixed') {   /* change discount to percentage by mohammed*/
                return response()->json('the type field must contain discount or fixed ');
            } if ($request->type!='percentage' && $request->type!='fixed') {
                return response()->json('the type field must contain discount or fixed ');
            }
            if ($request->apply_to!='enterprise' && $request->apply_to!='customer' && $request->apply_to!='all') {
                return response()->json('the apply to field must contain enterprise or customer or all ');
            }

            DB::beginTransaction();
            $coupon->update($request->validated());
                $data = [
                    'title' =>'Update',
                    'body' => "Update",
                    'target' => 'coupon',
                    'link'  => route('admin.coupons.index', [ 'name' => $coupon['name']]),
                    'target_id' => $coupon['name'],
                    'sender' => auth()->user()->name,
                ];
                $this->sendNotification($data);
                DB::commit();
            return $this->sendResponse($coupon, 'edit coupons success');
        } catch (\Excaption $th) {
            return response()->json('Error: '.$th);
           }
    }

    public function destroy($id){
        try {
            $coupon = Coupon::findOrFail($id);
            if (auth()->user()->id !=$coupon->user_id && auth()->user()->type != 'admin'  && auth()->user()->type !='superadministrator' && auth()->user()->type != 'emp') {
                return response()->json('action is denied ');
            }
            if ($coupon) {
                DB::beginTransaction();
                $coupon->delete();
                    $data = [
                        'title' =>'disable',
                        'body' => "disable",
                        'target' => 'coupon',
                        'link'  => route('admin.coupons.index', [ 'name' => $coupon['name']]),
                        'target_id' =>$coupon['name'],
                        'sender' => auth()->user()->name,
                    ];
                    $this->sendNotification($data);
                    DB::commit();
                    return response()->json('success');
            }
        } catch (Excaption $th) {
            return response()->json('Error: '.$th);
           }

    }

    public function restore($id){
        try {
            $coupon = Coupon::onlyTrashed()->findOrFail($id);
            if (auth()->user()->type != 'admin'  && auth()->user()->type !='superadministrator' && auth()->user()->type != 'emp') {
                return response()->json('action is denied ' );
            }
            if ($coupon) {

                DB::beginTransaction();
                $coupon->restore();
                    $data = [
                        'title' =>'Restore',
                        'body' => "Restore",
                        'target' => 'coupon ',
                        'link'  => route('admin.coupons.index', [ 'name' => $coupon['name']]),
                        'target_id' =>$coupon['name'],
                        'sender' => auth()->user()->name,
                    ];
                    $this->sendNotification($data);
                    DB::commit();
                    return response()->json('success');

            }

        } catch (Excaption $th) {
            return response()->json('Error: '.$th);
           }
    }

    private function sendNotification($data){
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
        }
    }
}

