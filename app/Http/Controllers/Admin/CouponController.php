<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CareersExport;
use App\Exports\CouponExport;
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
use Maatwebsite\Excel\Facades\Excel;
use App\Notifications\FcmPushNotification;

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
        $this->middleware(['permission:coupons_export'])->only('export');
    }
    public function index (Request $request){
            if (session('success')) {
                toast(session('success'), 'success');
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
            ->get();
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
        $msg=Coupon::create($coupon);
        $this->notifyCouponEvent('add', $coupon['name']);

        DB::commit();
        session()->flash('success', __('site.added_success'));
        return redirect()->route('admin.coupons.index');

    } catch (\Exception $e) {
        DB::rollBack();
           
        session()->flash('error_message', __('site.something_wrong'));
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
            $coupon->update($request->validated());
            $this->notifyCouponEvent('edit', $coupon->name);
            DB::commit();
            session()->flash('success', __('site.edited_success'));
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
                $this->notifyCouponEvent('disable', $coupon['name']);
                    DB::commit();
                    session()->flash('success', __('site.disable_success'));
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
                $this->notifyCouponEvent('enable', $coupon['name']);
                    DB::commit();
                    session()->flash('success', __('site.enable_success'));
                    return redirect()->route('admin.coupons.index');

            }
            session()->flash('errors', __('site.coupon_not_found'));
            return redirect()->route('admin.coupons.index');
    }

    private function notifyCouponEvent(string $titleKey, string $couponName): void
    {
        // Common fields
        $link = route('admin.coupons.index', ['name' => $couponName]);
        $sender = auth()->user()->name ?? 'System';

        // Admin payload (translation keys)
        $dataAdmin = [
            'title'     => $titleKey,
            // omit body key; admin view composes with title + target
            'target'    => 'coupon',
            'object'    => ['name' => $couponName],
            'link'      => $link,
            'target_id' => $couponName,
            'sender'    => $sender,
        ];

        // Frontend payload (literal localized strings)
        $dataFront = [
            'title' => [
                'ar' => Lang::get('site.' . $titleKey, [], 'ar'),
                'en' => Lang::get('site.' . $titleKey, [], 'en'),
            ],
            // body omitted to allow client to compose message from title + target + target_id
            'target'    => 'coupon',
            'object'    => ['name' => $couponName],
            'link'      => $link,
            'target_id' => $couponName,
            'sender'    => $sender,
        ];

        // FCM title/message for push
        $fcmTitle = Lang::get('site.' . $titleKey);
        $fcmMessage = $fcmTitle . ' ' . Lang::get('site.coupon') . ' ' . $couponName . ' ' . Lang::get('site.by') . ' ' . $sender;

        // Admins
        $admins = User::whereIn('type', ['admin','superadministrator'])->get();
        foreach ($admins as $admin) {
            Notification::send($admin, new LocalNotification($dataAdmin));
            if (!empty($admin->fcm_token)) {
                Notification::send($admin, new FcmPushNotification($fcmTitle, $fcmMessage, [$admin->fcm_token]));
            }
        }

        // Providers and seekers as announcement
        $providers = User::where('user_type','service_provider')->get();
        $seekers   = User::whereIn('type', ['user','factory'])->get();
        foreach ($providers as $prov) {
            Notification::send($prov, new LocalNotification($dataFront));
            if (!empty($prov->fcm_token)) {
                Notification::send($prov, new FcmPushNotification($fcmTitle, $fcmMessage, [$prov->fcm_token]));
            }
        }
        foreach ($seekers as $seeker) {
            Notification::send($seeker, new LocalNotification($dataFront));
            if (!empty($seeker->fcm_token)) {
                Notification::send($seeker, new FcmPushNotification($fcmTitle, $fcmMessage, [$seeker->fcm_token]));
            }
        }
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
            $data = [
                'title' => 'disable',
                'body' => 'disable_body',
                'target' => 'coupon',
                'link'  => route('admin.coupons.index', ['name' => $coupon->name]),
                'target_id' => $coupon->name,
                'sender' => auth()->user()->name,
            ];

            $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
            foreach ($users as $user1) {
                Notification::send($user1, new LocalNotification($data));
            }
            session()->flash('success', __('site.disable_success'));
        } elseif ($coupon->active == 0) {
            $coupon->active = 1;
            $coupon->save();
            $data = [
                'title' => 'enable',
                'body' => 'enable_body',
                'target' => 'coupon',
                'link'  => route('admin.coupons.index', ['name' => $coupon->name]),
                'target_id' => $coupon->name,
                'sender' => auth()->user()->name,
            ];
            $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
            foreach ($users as $user1) {
                Notification::send($user1, new LocalNotification($data));
            }
            session()->flash('success', __('site.enable_success'));
        }
        return redirect()->route('admin.coupons.index');
    }

    public function export()
    {

        return Excel::download(new CouponExport,  Lang::get('site.coupons') . '-' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx');
    }
}
