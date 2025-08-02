<?php

namespace App\Exports;

use App\Models\Article_category;
use App\Models\Career;
use App\Models\Coupon;
use Illuminate\Support\Facades\Lang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ArticleCategoryExport implements FromCollection ,WithHeadings, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function headings(): array
    {
        return [
            'id' => Lang::get('site.num'),
            'category_en' => Lang::get('site.category_en'),
            'category_ar' => Lang::get('site.category_ar'),

        ];
    }

    public function collection()
    {
        return Article_category::select('id', 'category_en', 'category_ar')->get();

    }
}
