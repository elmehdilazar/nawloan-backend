<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use anlutro\LaravelSettings\Facade as Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting([
            'app_name_ar'           =>      'Nawloan',
            'app_name_en'           =>      'Nawloan',
            'email'                 =>      'info@nawloan.net',
            'site_link'             =>      'https://nawloan.net',
            'phone'                 =>      '9338473434783',
            'address_ar'            =>      'المملكة العربية السعودية , الرياض',
            'address_en'            =>      '',
            'terms_conditions'      =>      '',
            'currency'              =>      'Egyptian Pound',
            'currency_atr'          =>      'GEP',
            'facebook_link'         =>      'https://facebook.com',
            'instagram_link'        =>      'https://instagram.com',
            'twitter_link'          =>      'https://twitter.com',
            'linkedin_link'         =>      'https://linkedin.com',
            'logo'                  =>      'uploads/img/logo.png',

            'favoico'               =>      'uploads/img/logo.png',
            'title'                 =>      'nawloan.net',
            'canonical'             =>      'https://nawloan.net',
            'keywords_ar'           =>      '',
            'keywords_en'           =>      '',
            'description_ar'        =>      '',
            'description_en'        =>      '',
            'og_site_name'          =>      'Nawloan',
            'og_type_ar'            =>      'خدمات',
            'og_type_en'            =>      'Services',
            'og_title'              =>      'Nawloan',
            'og_url'                =>      'https://nawloan.net',
            'og_image'              =>      'uploads/img/logo.png',
            'og_description_ar'     =>      '',
            'og_description_en'     =>      '',
            'twitter_card_ar'       =>      'summary',
            'twitter_card_en'       =>      'summary',
            'twitter_domain'        =>      'nawloan.net',
            'twitter_title_ar'      =>      'Nawloan',
            'twitter_title_en'      =>      'Nawloan',
            'twitter_description_ar'=>      '',
            'twitter_description_en'=>      '',
            'fine'                  =>      '500',
            'avg_fuel_consumption_per_10_km' =>  '1',
            'diesel_cost_per_km'   =>       '0.1',
            'liter_price'           =>      '500',
            'operating_costs'       =>      '1000',
            'expenses'              =>      '500',
            'driver_commission'     =>      '15',
            'company_commission'    =>      '10',
            'customer_fee'          =>      '50',
            'company_fee'           =>      '50',
        ])->save();
    }
}
