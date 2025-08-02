<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Facades\Lang;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class DriversExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
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
            $user->userData->national_id ?? '',
            $user->userData->driving_license_number ?? '',
            $user->userData->track_license_number ?? '',
            $user->userData->car->name_ar ?? '',
            $user->userData->track_number ?? '',
            $user->userData->company_id ?? '',
            $user->userData->vip ,
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
            'userData.national_id' => Lang::get('site.national_id'),
            'userData.driving_license_number' => Lang::get('site.driving_license_number'),
            'userData.track_license_number' => Lang::get('site.track_license_number'),
            'userData.car.name_ar' => Lang::get('site.car'),
            'userData.track_number' => Lang::get('site.track_number'),
            'userData.vip' => Lang::get('site.vip'),
            'userData.company_id' => Lang::get('site.shipping_company'),
            'active' => Lang::get('site.status'),
            'created_at' => Lang::get('site.created_at'),
            'updated_at' => Lang::get('site.updated_at')
        ];
    }
    public function collection()
    {
        return User::select('id', 'name', 'email','phone', 'type', 'active', 'created_at', 'updated_at')->with('userData')->where('type', 'driver')->get();
    }
}
