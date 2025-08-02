<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentMethod::create([
            'name'      =>  'Paypal',
            'active'    =>  1
        ]);
        PaymentMethod::create([
            'name'      =>  'Visa',
            'active'    =>  1
        ]);
        PaymentMethod::create([
            'name'      =>  'MasterCard',
            'active'    =>  1
        ]);
        PaymentMethod::create([
            'name'      =>  'Madaa',
            'active'    =>  1
        ]);
        PaymentMethod::create([
            'name'      =>  'StcPay',
            'active'    =>  1
        ]);
    }
}
