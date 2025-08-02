<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Facades\Lang;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomersExport implements FromCollection, WithMapping, WithHeadings,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

    /*  public function collection()
    {
        return User::select('id', 'name', 'phone', 'type', 'active', 'created_at', 'updated_at')->get();
    }*/
    use Exportable;
    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email ?? '',
            $user->phone,
            $user->type,
            $user->userData->vip,
            $user->active,
            $user->created_at,
            $user->updated_at,

        ];
    }

    public function headings(): array
    {
        return [
            'id' => Lang::get('site.num'),
            'name' => Lang::get('site.name'),
            'email' => Lang::get('site.email'),
            'phone' => Lang::get('site.phone'),
            'type' => Lang::get('site.type'),
            'userData.vip' => Lang::get('site.vip'),
            'active' => Lang::get('site.status'),
            'created_at' => Lang::get('site.created_at'),
            'updated_at' => Lang::get('site.updated_at')
        ];
    }
    public function collection()
    {
        return User::select('id','name','email','phone','type','active', 'created_at', 'updated_at')->with('userData')->where('type','user')->get();
    }
}
