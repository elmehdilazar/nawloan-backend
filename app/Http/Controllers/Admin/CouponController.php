<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CareersExport;
use App\Exports\CouponExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Translation\Translator;
use App\Http\Requests\CouponRequest;
use App\Jobs\SendCouponNotifications;
use App\Models\Coupon;
use App\Models\User;
use App\Notifications\LocalNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class CouponController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        //create read update delete
        $this->middleware(['permission:coupons_read'])->only('index');
        $this->middleware(['permission:coupons_create'])->only('create');
        $this->middleware(['permission:coupons_update'])->only('edit');
        $this->middleware(['permission:coupons_enable'])->only('restore');
        $this->middleware(['permission:coupons_disable'])->only('destroy');
        $this->middleware(['permission:coupons_disable'])->only('destroySelected');
        $this->middleware(['permission:coupons_export'])->only('export');
    }
    public function index (Request $request){
            if (session('success')) {
                Alert::toast(session('success'), 'success');
            }
            if($request->has('quick_search')){
                $coupons = $this->quickSearch($request) ;
                return view('admin.coupons.index', ['coupons' => $coupons]);
            }
        if ($request->active == 1) {
            $coupons = Coupon::when($request->name, function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
            })->when($request->code, function ($query) use ($request) {
                return $query->where('code', 'like', '%' . $request->code . '%');
            })->select()->inactive()->latest()->paginate(10);
            return view('admin.coupons.index', ['coupons' => $coupons]);
        } elseif ($request->active == 2) {
            $coupons = Coupon::when($request->name, function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
            })->when($request->code, function ($query) use ($request) {
                return $query->where('code', 'like', '%' . $request->code . '%');
            })->select()->active()->latest()->paginate(10);
            return view('admin.coupons.index', ['coupons' => $coupons]);
        } elseif ($request->active == 0) {
            $coupons = Coupon::when($request->name, function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
            })->when($request->code, function ($query) use ($request) {
                return $query->where('code', 'like', '%' . $request->code . '%');
            })->select()->latest()->paginate(10);
            return view('admin.coupons.index', ['coupons' => $coupons]);
        }

        // Default listing when no active filter is provided
        $coupons = Coupon::when($request->name, function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
            })->when($request->code, function ($query) use ($request) {
                return $query->where('code', 'like', '%' . $request->code . '%');
            })
            ->select()->latest()->paginate(10);
        return view('admin.coupons.index', ['coupons' => $coupons]);


    }

    private function quickSearch(Request $request ){

            $coupons = Coupon::where('name','like','%'.$request->quick_search.'%')
            ->orWhere('code','like','%'.$request->quick_search.'%')
            ->orWhere('number_availabe','like','%'.$request->quick_search.'%')
            ->orWhere('discount','like','%'.$request->quick_search.'%')
            ->orWhere('apply_to','like','%'.$request->quick_search.'%')
            ->orWhere('start_date','like','%'.$request->quick_search.'%')
            ->orWhere('expiry_date','like','%'.$request->quick_search.'%')
            ->latest()->paginate(10);
            return $coupons;

    }
    public function create()
    {
        return view('admin.coupons.create');
    }

/*    public function store(CouponRequest $request){
        //dd($request);
        if ($request->type!='discount' && $request->type!='fixed') {
            session()->flash('errors', 'the type field must contain discount or fixed');
            return back()->withInput();
        }
        if ($request->apply_to!='enterprise'&& $request->apply_to!='customer'&& $request->apply_to!='all') {
            session()->flash('errors', 'the apply to field must contain enterprise or customer or all ');
            return back()->withInput();
        }
        DB::beginTransaction();
        $coupon = $request->validated();
        $coupon['user_id'] = auth()->user()->id;
        $coupon['active'] = 1;
        $coupon['name'] = $request->name;
        $coupon['code'] = $request->code;
        $coupon['number_availabe'] = $request->number_availabe;
        $coupon['start_date'] = $request->start_date;
        $coupon['expiry_date'] = $request->expiry_date;
        $coupon['type'] = $request->type;
        $coupon['discount'] = $request->discount;
        $coupon['apply_to'] = $request->apply_to;
        Coupon::create($coupon);
         $data = [
             'title' =>'add',
             'body' => "add_body ",
             'target' => 'coupon',
             'link'  => route('admin.coupons.index', [ 'name' => $coupon['name']]),
             'target_id' => $coupon['name'],
             'sender' => auth()->user()->name,
             ];
         $this->sendNotification($data);
         DB::commit();
         session()->flash('success', __('site.added_success'));
        return redirect()->route('admin.coupons.index');

    }*/
    public function store(CouponRequest $request)
{
    // Validation is already handled by CouponRequest
    

    DB::beginTransaction();

    try {
        $coupon = $request->validated();
        $coupon['user_id'] = auth()->user()->id;

        // Double-check allowed values for type and apply_to using Rule in CouponRequest instead
        $msg = Coupon::create($coupon);

        DB::commit();

        // Queue notifications in background with bilingual content after commit
        $this->notifyCouponEvent('add', $msg);
        session()->flash('success', __('site.added_success'));
        Alert::toast(__('site.added_success'), 'success');
        return redirect()->route('admin.coupons.index');

    } catch (\Exception $e) {
        DB::rollBack();
           
        session()->flash('error_message',$e->getMessage() );
        return back()->withInput();
    }
}

    public function edit($id)
    {
        $coupon = Coupon::find($id);
        if (!$coupon) {
            session()->flash('errors', __('site.coupon_not_found'));
            return redirect()->route('admin.coupons.index');
        }
        return view('admin.coupons.edit', ['coupon' => $coupon]);
    }
    public function update(CouponRequest $request , $id)
    {
        $coupon = Coupon::withTrashed()->findOrFail($id);
        DB::beginTransaction();
        try {
            $data = $request->validated();
            if (!isset($data['active'])) {
                $data['active'] = $coupon->active ?? 1;
            }
            $coupon->update($data);
            DB::commit();
            $this->notifyCouponEvent('edit', $coupon);
            session()->flash('success', __('site.edited_success'));
            Alert::toast(__('site.edited_success'), 'success');
            return redirect()->route('admin.coupons.index');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error_message', __('site.something_wrong'));
            return back()->withInput();
        }
    }

    public function destroy($id){
            $coupon = Coupon::findOrFail($id);
            if ($coupon) {
                DB::beginTransaction();
                $coupon->delete();
                    DB::commit();
                    $this->notifyCouponEvent('disable', $coupon);
                    session()->flash('success', __('site.disable_success'));
                    Alert::toast(__('site.disable_success'), 'success');
                    return redirect()->route('admin.coupons.index');
            }
            session()->flash('errors', __('site.coupon_not_found'));
            return redirect()->route('admin.coupons.index');

    }

    public function restore($id){
            $coupon = Coupon::onlyTrashed()->findOrFail($id);
            if ($coupon) {

                DB::beginTransaction();
                $coupon->restore();
                    DB::commit();
                    $this->notifyCouponEvent('enable', $coupon);
                    session()->flash('success', __('site.enable_success'));
                    Alert::toast(__('site.enable_success'), 'success');
                    return redirect()->route('admin.coupons.index');

            }
            session()->flash('errors', __('site.coupon_not_found'));
            return redirect()->route('admin.coupons.index');
    }

    private function notifyCouponEvent(string $titleKey, Coupon $coupon): void
    {
        $sender = auth()->user()->name ?? 'System';

        SendCouponNotifications::dispatch($titleKey, $coupon->id, $sender)
            ->afterCommit();
    }

    public function changeStatus($id)
    {
        $coupon = Coupon::select()->find($id);
        if (!$coupon) {
            session()->flash('errors', __('site.Coupon_not_found'));
            return redirect()->route('admin.Coupon.index');
        }
        if ($coupon->active == 1) {
            $coupon->active = 0;
            $coupon->save();
            // Notify based on coupon apply_to
            $this->notifyCouponEvent('disable', $coupon);
            session()->flash('success', __('site.disable_success'));
        } elseif ($coupon->active == 0) {
            $coupon->active = 1;
            $coupon->save();
            // Notify based on coupon apply_to
            $this->notifyCouponEvent('enable', $coupon);
            session()->flash('success', __('site.enable_success'));
        }
        return redirect()->route('admin.coupons.index');
    }

    public function export()
    {

        return Excel::download(new CouponExport,  Lang::get('site.coupons') . '-' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx');
    }

    public function destroySelected(Request $request)
    {
        $ids = $request->query('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }
        $ids = array_values(array_unique(array_map('intval', (array) $ids)));
        // keep only positive integers
        $ids = array_values(array_filter($ids, function ($id) { return $id > 0; }));

        if (empty($ids)) {
            return back()->with('error', __('site.no_items_selected'));
        }

        $deleted = Coupon::whereIn('id', $ids)->delete();
        if ($deleted < 1) {
            return back()->with('error', __('site.no_items_selected'));
        }

        return back()->with('success', __('site.deleted_success'));
    }
}
