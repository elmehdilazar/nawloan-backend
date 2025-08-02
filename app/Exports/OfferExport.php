<?php

namespace App\Exports;

use App\Models\Offer;
use Illuminate\Support\Facades\Lang;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OfferExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     return Offer::all();
    // }

    use Exportable;
    public function map($offer): array
    {
        return [
            $offer->id,
            $offer->price,
            $offer->user->name,
            $offer->driver->name,
            $offer->order_id,
            $offer->status,
            $offer->created_at,
            $offer->updated_at,

        ];
    }

    public function headings(): array
    {
        return [
            'id' => Lang::get('site.num'),
            'price' => Lang::get('site.price'),
            'user.name' => Lang::get('site.service_provider'),
            'driver.name' => Lang::get('site.driver'),
            'order_id' => Lang::get('site.order_number'),
            'active' => Lang::get('site.status'),
            'created_at' => Lang::get('site.created_at'),
            'updated_at' => Lang::get('site.updated_at')
        ];
    }
    public function collection()
    {
        return Offer::select('id', 'price', 'user_id','driver_id', 'order_id', 'status', 'created_at', 'updated_at')->with(['order','user','driver'])->get();
    }
}
