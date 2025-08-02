<?php

namespace App\Exports;

use App\Models\ShipmentType;
use Illuminate\Support\Facades\Lang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ShipmentsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     return ShipmentType::all();
    // }

    public function headings(): array
    {
        return [
            'id' => Lang::get('site.num'),
            'name_ar' =>Lang::get('site.name_ar'),
            'name_en' => Lang::get('site.name_en'),
            'active' => Lang::get('site.status'),
            'created_at' => Lang::get('site.created_at'),
            'updated_at' => Lang::get('site.updated_at')
        ];
    }
    public function collection()
    {
        return ShipmentType::select('id', 'name_ar', 'name_en', 'active', 'created_at', 'updated_at')->get();
    }
}
