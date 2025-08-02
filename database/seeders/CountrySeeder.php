<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        Country::create([
            'name' => 'BAHRAIN',
            'country_code'=>'bh',
            'phone_code' => '+973',
        ]);
        Country::create([
            'name' => 'EGYPT',
            'country_code'=>'eg',
            'phone_code' => '+20',
        ]);
        Country::create([
            'name' => 'Saudi Arabia',
            'country_code' => 'sa',
            'phone_code' => '+966',
        ]);
        Country::create([
            'name'=>'Qatar',
            'country_code'=> 'qa',
            'phone_code'=>'+974'
        ]);
        Country::create([
            'name' => 'United Arab Emirates',
            'country_code' => 'ae',
            'phone_code' => '+971',
        ]);
        Country::create([
            'name' => 'United State',
            'country_code' => 'us',
            'phone_code' => '+1',
        ]);
        Country::create([
            'name' => 'United Kingdom',
            'country_code' => 'gb',
            'phone_code' => '+44',
        ]);
    }
}
