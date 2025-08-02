<?php

namespace App\Exports;

use App\Models\Country;
use Illuminate\Support\Facades\Lang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CountriesCodesExport implements FromCollection,WithHeadings,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

/*  public function collection()
    {
        return User::select('id', 'name', 'phone', 'type', 'active', 'created_at', 'updated_at')->get();
    }*/
    public function headings(): array
    {
        return [
            'id' => Lang::get('site.num'),
            'name' => Lang::get('site.country_name'),
            'country_code' => Lang::get('site.country_code'),
            'phone_code' => Lang::get('site.phone_code'),
            'active' => Lang::get('site.status'),
            'created_at' => Lang::get('site.created_at'),
            'updated_at' => Lang::get('site.updated_at')
        ];
    }
    public function collection()
    {
        return Country::select('id','name','country_code','phone_code','active', 'created_at', 'updated_at')->get();
    }
}
