<?php

namespace App\Exports;

use App\Models\Career;
use Illuminate\Support\Facades\Lang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CareersExport implements FromCollection ,WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function headings(): array
    {
        return [
            'id' => Lang::get('site.num'),
            'name_ar' => Lang::get('site.name_ar'),
            'name_en' => Lang::get('site.name_en'),
            'category_id' => Lang::get('site.category_id'),
            'category_ar' => Lang::get('site.category'),
            'category_en' => Lang::get('site.category'),
            'desc_en' => Lang::get('site.desc'),
            'desc_ar' => Lang::get('site.desc'),
            'active' => Lang::get('site.status'),
            'created_at' => Lang::get('site.created_at'),
            'updated_at' => Lang::get('site.updated_at')
        ];
    }

    public function collection()
    {
        $careers =  Career::all();
        foreach ($careers as $item ) {
            $item->category->category_ar;
            $item->category->category_ar;
        }

        // dd($careers);
        return $careers ;
    }
}
