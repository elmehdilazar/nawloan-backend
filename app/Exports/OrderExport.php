<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Facades\Lang;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrderExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;
    public function map($order): array
    {
        return [
            $order->id,
            $order->user->name ?? '',
            $order->serviceProvider->name ?? '',
            $order->car->name_ar ?? '',
            $order->shipmentType->name_ar ?? '',
            $order->paymentType->name ?? '',
            $order->pick_up_address ?? '',
            $order->drop_of_address ?? '',
            $order->spoil_quickly ?? '',
            $order->breakable ?? '',
            $order->size ?? '',
            $order->weight_ton . ' ' . Lang::get('site.ton') ?? '',
            $order->ton_price . ' ' . setting('currency_atr' ?? ''),
            $order->shipping_date ?? '',
            $order->status,
            $order->created_at,
            $order->updated_at,

        ];
    }

    public function headings(): array
    {
        return [
            'id' => Lang::get('site.num'),
            'user.name' => Lang::get('site.customer'),
            'serviceProvider.name'=>Lang::get('site.driver'),
            'car.name_ar' => Lang::get('site.car'),
            'shipmentType.name_ar' => Lang::get('site.shipment_type'),
            'paymentType.name_ar' => Lang::get('site.payment_method'),
            'pick_up_address' => Lang::get('site.pick_up_address'),
            'drop_of_address' => Lang::get('site.drop_of_address'),
            'spoil_quickly' => Lang::get('site.spoil_quickly'),
            'breakable' => Lang::get('site.breakable'),
            'size' => Lang::get('site.size'),
            'weight_ton' => Lang::get('site.weight_ton') ,
            'ton_price' => Lang::get('site.ton_price'),
            'shipping_date' => Lang::get('site.shipping_date'),
            'active' => Lang::get('site.status'),
            'created_at' => Lang::get('site.created_at'),
            'updated_at' => Lang::get('site.updated_at')
        ];
    }
    public function collection()
    {
        return Order::select('id',
        'user_id', 'car_id', 'pick_up_address', 'drop_of_address', 'shipment_type_id', 'spoil_quickly', 'breakable',
        'size', 'weight_ton','ton_price', 'shipping_date', 'status', 'service_provider','payment_method_id', 'created_at', 'updated_at')->
        with([ 'user', 'paymentType','shipmentType','serviceProvider'])->get();
    }
}
