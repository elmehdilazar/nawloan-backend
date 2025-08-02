<?php

namespace Database\Seeders;

use App\Models\Car;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Car::create([
            'name_en'   =>  'jumbo',
            'name_ar'   =>  'نص نقل ',
            'image'     =>  '/uploads/cars/نص_نقل_jumbo.png',
            'weight'    =>  '20',
            'active'    =>  1
        ]);
        Car::create([
            'name_en'   =>  'Flat bed',
            'name_ar'   =>  'تريلا',
            'image'     =>  '/uploads/cars/تريلا_Flat_bed.png',
            'weight'    =>  '32',
            'active'    =>  1
        ]);
        Car::create([
            'name_en'   =>  'closed Flat bed',
            'name_ar'   =>  'تريلا صندوق',
            'image'     =>  '/uploads/cars/تريلا_صندوق_closed_Flat_bed.png',
            'weight'    =>  '42',
            'active'    =>  1
        ]);
        Car::create([
            'name_en'   =>  'Double flat bed',
            'name_ar'   =>  'جرار ومقطورة',
            'image'     =>  '/uploads/cars/جرار_ومقطورة_Double_flat_bed.png',
            'weight'    =>  '25',
            'active'    =>  1
        ]);
        Car::create([
            'name_en'   =>  'Container double flat bed',
            'name_ar'   =>  'جرار ومقطورة كونتر ',
            'image'     =>  '/uploads/cars/جرار_ومقطورة_كونتر_Container_double_flat_bed.png',
            'weight'    =>  '30',
            'active'    =>  1
        ]);
        Car::create([
            'name_en'   =>  'Pick up',
            'name_ar'   =>  'ربع نقل',
            'image'     =>  '/uploads/cars/ربع_نقل_Pick_up.png',
            'weight'    =>  '25',
            'active'    =>  1
        ]);
        Car::create([
            'name_en'   =>  ' Closed jumbo',
            'name_ar'   =>  'نص نقل صندوق',
            'image'     =>  '/uploads/cars/نص_نقل_صندوق_Closed_jumbo.png',
            'weight'    =>  '20',
            'active'    =>  1
        ]);
        Car::create([
            'name_en'   =>  'Faradany truck',
            'name_ar'   =>  'فرادانى ',
            'image'     =>  '/uploads/cars/فرادانى_Faradany_truck.png',
            'weight'    =>  '2',
            'active'    =>  1
        ]);
        Car::create([
            'name_en'   =>  'Closed pick up',
            'name_ar'   =>  'ربع نقل صندوق',
            'image'     =>  '/uploads/cars/ربع_نقل_صندوق_Closed_pick_up.png',
            'weight'    =>  '4',
            'active'    =>  1
        ]);
        Car::create([
            'name_en'   =>  'Dump truck',
            'name_ar'   =>  'نقل قلاب ',
            'image'     =>  'uploads/cars/نقل_قلاب_Dump_truck.png',
            'weight'    =>  '5',
            'active'    =>  1
        ]);
    }
}
