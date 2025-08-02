<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $user= User::create([
            'name'      =>  'Super Admin',
            'phone'     =>  '+201212121212',
            'phone_verified_at' => now(),
            'email'     =>  'super_admin@app.com',
            'email_verified_at' => now(),
            'password'  =>  bcrypt('123456'),
            'type'      =>  'superadministrator',
            'user_type' =>  'superadministrator',
            'active'    =>  1,
        ]);
        UserData::create(['image'=>'uploads/users/default.png','user_id'=>$user->id,'type'=>$user->type,'phone'=> '+201212121212']);
        $user->attachRole('superadministrator');

        $user = User::create([
            'name'      =>  ' Admin',
            'phone'     =>  '+201212121211',
            'phone_verified_at' => now(),
            'email'     =>  'admin@app.com',
            'email_verified_at' => now(),
            'password'  =>  bcrypt('123456'),
            'type'      =>  'admin',
            'user_type' =>  'admin',
            'active'    =>  1,
        ]);
        UserData::create(['image' => 'uploads/users/default.png', 'user_id' => $user->id, 'type' => $user->type, 'phone' => '+201212121212']);
        $user->attachRole('admin');
        $user = User::create([
            'name'      =>  ' Employee',
            'phone'     =>  '+201212121210',
            'phone_verified_at' => now(),
            'email'     =>  'emp@app.com',
            'email_verified_at' => now(),
            'password'  =>  bcrypt('123456'),
            'type'      =>  'emp',
            'user_type' =>  'emp',
            'active'    =>  1,
        ]);
        UserData::create(['image' => 'uploads/users/default.png', 'user_id' => $user->id, 'type' => $user->type, 'phone' => '+201212121212']);
        $user->attachRole('emp');
    }
}
