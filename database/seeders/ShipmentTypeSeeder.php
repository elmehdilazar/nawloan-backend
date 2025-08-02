<?php

namespace Database\Seeders;

use App\Models\ShipmentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShipmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ShipmentType::create([
            'name_en'   =>      'Foods',
            'name_ar'   =>      'أطعمة',
            'active'    =>      1
        ]);
        ShipmentType::create([
            'name_en'   =>      'Products',
            'name_ar'   =>      'منتجات',
            'active'    =>      1
        ]);
        ShipmentType::create([
            'name_en'   =>      'Cars',
            'name_ar'   =>      'سيارات',
            'active'    =>      1
        ]);
        ShipmentType::create([
            'name_en'   =>      'Electronic Devices',
            'name_ar'   =>      'أجهزة إلكترونية',
            'active'    =>      1
        ]);
        ShipmentType::create([
            'name_en'   =>      'Computers',
            'name_ar'   =>      'أجهزة حاسوب',
            'active'    =>      1
        ]);
        ShipmentType::create([
            'name_en'   =>      'Oils',
            'name_ar'   =>      'زيوت',
            'active'    =>      1
        ]);
    }
}
