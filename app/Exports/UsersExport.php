<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Facades\Lang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class UsersExport implements FromCollection,WithHeadings,ShouldAutoSize
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
            'id'=>Lang::get('site.num'),
            'name'=> Lang::get('site.name'),
            'phone'=>Lang::get('site.phone'),
            'type'=> Lang::get('site.type'),
            'active'=> Lang::get('site.status'),
            'created_at'=> Lang::get('site.created_at'),
            'updated_at'=> Lang::get('site.updated_at')
        ];
    }
    public function collection()
    {
        return User::select('id','name','phone','type','active', 'created_at', 'updated_at')->where('user_type','manage')->where('type','!=', 'superadministrator')->get();
    }
}
