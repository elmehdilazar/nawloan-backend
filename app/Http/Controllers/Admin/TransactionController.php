<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayTransaction;
use App\Models\Transaction;
use App\Models\User;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Lang;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //create read update delete
        $this->middleware(['permission:transactions_read'])->only('index');
        $this->middleware(['permission:transactions_create'])->only('create');
        $this->middleware(['permission:transactions_update'])->only('edit');
        $this->middleware(['permission:transactions_enable'])->only('changeStatus');
        $this->middleware(['permission:transactions_disable'])->only('changeStatus');
        $this->middleware(['permission:transactions_disable'])->only('destroySelected');
    }
    public function index(Request $request)
    {
        if (session('success')) {
            toast(session('success'), 'success');
        }
        $users=User::select('id','name')->where('user_type','service_seeker')->get();
        $payment_methods=PaymentMethod::select('id','name')->get();
        $payTran= PayTransaction::when($request->search, function ($query) use ($request) {
            return $query->where('transaction_id',  $request->search)
                    ->orWhere('payment_method',$request->search)
                    ->orWhere('name_in_card',$request->search)
                    ->orWhere('payment_type',$request->search)
                    ->orWhere('order_id',$request->search)
                    ->orWhere('status',$request->search)
                    ->orWhere('fee',$request->search)
                    ->orWhere('amount',$request->search)
                    ->orWhere('id',$request->search);
        })->when($request->transaction_id, function ($query) use ($request) {
            return $query->where('transaction_id',  $request->transaction_id);
        })->get()->first();
        $trans = Transaction::when($request->search, function ($query) use ($request) {
            return $query->where('id',  $request->search)
                    ->orWhere('pay_transaction_id',$request->search)
                    ->orWhere('order_id',$request->search)
                    ->orWhere('user_id',$request->search)
                    ->orWhere('price',$request->search)
                    ->orWhere('currency',$request->search)
                    ->orWhere('notes',$request->search);
        })->when($request->payment_method, function ($query) use ($request) {
            return $query->where('payment_method_id',  $request->payment_method );
        })->when($request->transaction_id, function ($query) use ($payTran) {
            return $query->where('pay_transaction_id',  $payTran->id );
        })->when($request->order_number, function ($query) use ($request) {
            return $query->where('order_id',  $request->order_number );
        })->when($request->amount, function ($query) use ($request) {
            return $query->where('price',  $request->amount);
        })->when($request->user_id, function ($query) use ($request) {
            return $query->where('user_id',  $request->user_id );
        })->when($request->start_date, function ($query) use ($request) {
            return $query->where('created_at','>=', $request->start_date);
        })->when($request->end_date, function ($query) use ($request) {
            return $query->where('created_at','<=', $request->end_date);
        })->with(['user', 'order', 'payTransaction', 'payMethod'])->
        latest()->orderBy('id', 'desc')->paginate(10);
        $trans1 = Transaction::with(['user', 'order', 'payTransaction', 'payMethod'])->latest()->orderBy('id', 'desc')->get();
        
        $total_income=0;
        $income=0;
        $profit=0;
        $total_profit=0;
        foreach($trans1 as $tran) {
            if($tran->order->serviceProvider && $tran->order->accountant){
            $total_income= $total_income+$tran->payTransaction->amount;
            
            if ($tran->order->serviceProvider->type == 'driver') {
                $income = $income+ ($tran->payTransaction->amount * $tran->order->accountant->service_provider_commission) / 100;
            }
            elseif ($tran->order->serviceProvider->type == 'driverCompany') {
                $income = $income + ($tran->payTransaction->amount * $tran->order->accountant->service_provider_commission) / 100;
            }
            $total_profit= $tran->payTransaction->amount - $tran->order->accountant->service_provider_amount;
            $profit
            = $total_profit - ($tran->order->accountant->service_seeker_fee +$tran->order->accountant->expenses +$tran->order->accountant->operating_costs ) ;
        }
        
    }
        
        $earn=[
            'income' => $income, 'total_income' => $total_income,'profit'=>$profit,'total_profit'=>$total_profit
        ];
        return view('admin.transactions.index',['trans'=>$trans,'users'=>$users, 'earn'=>$earn,'payment_methods'=>$payment_methods]);
    }
    public function export(){
      return Excel::download(new TransactionsExport,  Lang::get('site.transactions').'-'.Carbon::now()->format('Y-m-d_H-i-s').'.xlsx');
    }

    public function destroySelected(Request $request)
    {
        $ids = $request->input('ids', $request->input('id', $request->query('ids', [])));
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }
        $ids = array_values(array_unique(array_map('intval', (array)$ids)));
        $ids = array_values(array_filter($ids, fn($id) => $id > 0));

        if (empty($ids)) {
            return back()->with('error', __('site.no_items_selected'));
        }

        Transaction::whereIn('id', $ids)->delete();
        return back()->with('success', __('site.deleted_success'));
    }
}
