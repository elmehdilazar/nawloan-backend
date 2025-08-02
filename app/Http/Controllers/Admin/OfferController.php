<?php

namespace App\Http\Controllers\Admin;

use App\Exports\OfferExport;
use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\OfferStatus;
use App\Models\User;
use App\Notifications\FcmPushNotification;
use App\Notifications\LocalNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;

class OfferController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //create read update delete
        $this->middleware(['permission:offers_read'])->only('index');
        $this->middleware(['permission:offers_create'])->only('create');
        $this->middleware(['permission:offers_update'])->only('edit');
        $this->middleware(['permission:offers_enable'])->only('changeStatus');
        $this->middleware(['permission:offers_disable'])->only('changeStatus');
        $this->middleware(['permission:offers_export'])->only('export');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (session('success')) {
            toast(session('success'), 'success');
        }
        $users = User::select('id', 'name', 'type')->where('user_type', 'service_provider')->get();
        $offers = Offer::when($request->start_date, function ($query) use ($request) {
                return $query->where('created_at','>=', $request->start_date);
            })
        ->when($request->end_date, function ($query) use ($request) {
                return $query->where('created_at','<=', $request->end_date);
            })->when($request->number, function ($query) use ($request) {
            return $query->where('id', $request->number);
        })->when($request->order_id, function ($query) use ($request) {
            return $query->where('order_id',  $request->order_id);
        })->when($request->user_id, function ($query) use ($request) {
            return $query->where('user_id', $request->user_id);
        })->when($request->driver_id, function ($query) use ($request) {
            return $query->where('driver_id', $request->driver_id);
        })->when($request->from, function ($query) use ($request) {
            return $query->where('price','>=', $request->from);
        })->when($request->to, function ($query) use ($request) {
            return $query->where('price', '<=', $request->to);
        })->when($request->status, function ($query) use ($request) {
            return $query->where('status', $request->status);
        })->select()->latest()->orderBy('id', 'desc')->paginate(10);
        return view('admin.offers.index', ['offers' => $offers, 'users' => $users]);
    }
    public function show($id)
    {
        $offer = Offer::with(['order', 'user', 'driver'])->find($id);
        if (!$offer) {
            session()->flash('errors', __('site.offer_not_found'));
            return redirect()->route('admin.offers.index');
        }
        $statuses = OfferStatus::where('offer_id', $offer->id)->paginate(5);
        return view('admin.offers.show', ['offer' => $offer, 'statuses' => $statuses]);
    }

    public function edit($id)
    {
        $offer = Offer::with(['order', 'user', 'driver'])->find($id);
        if (!$offer) {
            session()->flash('errors', __('site.offer_not_found'));
            return redirect()->route('admin.offers.index');
        }
        $users=User::select('id','name')->where('user_type','service_provider')->get();
        $drivers = User::select('id', 'name')->where('type', 'driver')->get();

        return view('admin.offers.edit', ['offer' => $offer,'users'=>$users, 'drivers'=> $drivers]);
    }
    public function update(Request $request,$id){
        $request->validate([
            'driver_id' =>  'required|exists:users,id',
            'price'     =>  'required|numeric',
            'desc'     =>  'nullable|string',
            'notes'     =>  'nullable|string',
        ]);
        $offer = Offer::find($id);
        if (!$offer) {
            session()->flash('errors', __('site.offer_not_found'));
            return redirect()->route('admin.offers.index');
        }
        DB::beginTransaction();
        $offer->update([
            'driver_id' =>  $request->driver_id,
            'price'     =>  $request->price,
            'desc'      =>  $request->desc,
            'notes'     =>  $request->notes
        ]);

        $data = [
            'title' => 'edit',
            'body' => 'edit_body',
            'target' => 'offer',
            'link'  => route('admin.offers.index', ['number' => $offer->id]),
            'target_id' => $offer->id,
            'sender' => auth()->user()->name,
        ];
        $message = Lang::get('site.not_edit_offer_msg') . ' ' . $offer->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user')  . ' ' . auth()->user()->name;
        $title = Lang::get('site.not_edit_offer');
        Notification::send($offer->user, new FcmPushNotification($title, $message, [$offer->user->fcm_token]));
        Notification::send($offer->order->user, new FcmPushNotification($title, $message, [$offer->order->user->fcm_token]));
        Notification::send($offer->user, new LocalNotification($data));
        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->orWhere('type', 'emp')->get();
        foreach ($users as $user) {
            Notification::send($user, new LocalNotification($data));
         //   Notification::send($user, new FcmPushNotification($title, $message, [$user->fcm_token]));
        }
        DB::commit();
        session()->flash('success', __('site.edited_success'));
        return redirect()->route('admin.offers.index');
    }
    public function export()
    {
        return Excel::download(new OfferExport,  Lang::get('site.offers') . '-' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx');
    }
}

