<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Facades\Lang;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class CompaniesExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
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
            $user->userData->location ?? '',
            $user->userData->commercial_record ?? '',
            $user->userData->tax_card ?? '',
            $user->bank->bank_name ?? '',
            $user->bank->branch_name ?? '',
            $user->bank->account_holder_name ?? '',
            $user->bank->account_number ?? '',
            $user->bank->soft_code ?? '',
            $user->bank->iban ?? '',
            $user->userData->vip ?? '', 
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
            'userData.location' => Lang::get('site.company_location'),
            'userData.commercial_record' => Lang::get('site.commercial_record'),
            'userData.tax_card' => Lang::get('site.tax_card'),
            'userData.bank.bank_name' => Lang::get('site.bank_name'),
            'userData.bank.branch_name' => Lang::get('site.branch_name'),
            'userData.bank.account_holder_name' => Lang::get('site.account_holder_name'),
            'userData.bank.account_number' => Lang::get('site.account_number'),
            'userData.bank.soft_code' => Lang::get('site.soft_code'),
            'userData.bank.iban' => Lang::get('site.iban'),
            'userData.vip' => Lang::get('site.vip'),
            'active' => Lang::get('site.status'),
            'created_at' => Lang::get('site.created_at'),
            'updated_at' => Lang::get('site.updated_at')
        ];
    }
    public function collection()
    {
        return User::select('id', 'name','email', 'phone', 'type', 'active', 'created_at', 'updated_at')->with(['userData','bank'])->where('type', 'driverCompany')->get();
    }
}
