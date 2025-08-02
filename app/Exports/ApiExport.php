<?php

namespace App\Exports;

use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Lang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ApiExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     return Car::all();
    // }
    public function headings(): array
    {
        return [
            'id' => Lang::get('site.num'),
            'name' => Lang::get('site.name'),
            'type' => Lang::get('site.type'),
            'secret_key' => Lang::get('site.secret_key'),
            'publishable_key' => Lang::get('site.publishable_key'),
            'active' => Lang::get('site.status'),
            'created_at' => Lang::get('site.created_at'),
            'updated_at' => Lang::get('site.updated_at')
        ];
    }
    public function collection()
    {
        return PaymentMethod::select('id', 'name', 'type', 'publishable_key', 'secret_key', 'active', 'created_at', 'updated_at')->get();
    }
}
