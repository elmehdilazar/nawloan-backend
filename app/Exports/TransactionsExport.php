<?php

namespace App\Exports;

use App\Models\Transaction;
use App\Models\PayTransaction;
use Illuminate\Support\Facades\Lang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;

class TransactionsExport implements FromCollection,WithHeadings,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

/*  public function collection()
    {
        return User::select('id', 'name', 'phone', 'type', 'active', 'created_at', 'updated_at')->get();
    }*/
   /**/
    use Exportable;
    public function map($user): array
    {
        return [
            $user->id,
            $user->order->user->name ?? '',
            $user->order->serviceProvider->name  ?? '',
            $user->order_id ?? '',
            $user->amount ?? '',
            ($user->payTransaction->amount * 15 / 100 )  ?? '',
            $user->payTransaction->fee  ?? '',
            $user->payTransaction->currency  ?? '',
            $user->payMethod->name ?? '',
            $user->payTransaction->payment_type ?? '',
            $user->status ?? '',
            $user->created_at ?? '',
            $user->updated_at ?? '',

        ];
    }

   public function headings(): array
    {
        return [
            'id'=>Lang::get('site.num'),
            'order->user->name'=>Lang::get('site.customer'),
            'order->serviceProvider->name'=>Lang::get('site.driver'),
            'order_id'=> Lang::get('site.order_number'),
            'payTransaction->amount'=>Lang::get('site.amount'),
            'payTransaction->amount'=>Lang::get('site.profit'),
            'payTransaction->fee'=>Lang::get('site.fee'),
            'payTransaction->currency'=>Lang::get('site.currency'),
            'payMethod->name'=>Lang::get('site.payment_method'),
            'payTransaction->payment_type'=>Lang::get('site.payment_type'),
            'payTransaction->status'=>Lang::get('site.status'),
            'created_at'=> Lang::get('site.created_at'),
            'updated_at'=> Lang::get('site.updated_at')
        ];
    }
    public function collection()
    {
        return Transaction::with(['user', 'order', 'payTransaction', 'payMethod'])->
        latest()->orderBy('id', 'desc')->get();
    }
}
