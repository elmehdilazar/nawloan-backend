<?php

namespace App\Exports;

use App\Models\Career;
use App\Models\Coupon;
use Illuminate\Support\Facades\Lang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CouponExport implements FromCollection ,WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function headings(): array
    {
        return [
            'id' => Lang::get('site.num'),
            'name' => Lang::get('site.name'),
            'code' => Lang::get('site.code'),
            'type' => Lang::get('site.discount_type'),
            'number_availabe' => Lang::get('site.number_availabe'),
            'start_date' => Lang::get('site.start_date'),
            'expiry_date' => Lang::get('site.expires'),
            'discount' => Lang::get('site.discount_amount'),
            'apply_to' => Lang::get('site.apply_to'),


        ];
    }

    public function collection()
    {
        return Coupon::select('id', 'name', 'code', 'type', 'number_availabe', 'start_date', 'expiry_date','discount','apply_to')->get();

    }
}
