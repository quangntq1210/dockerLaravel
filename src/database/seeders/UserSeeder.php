<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    
     public function run()
    {
        // Tạo tài khoản Admin mẫu
        User::updateOrCreate(
            ['email' => 'client@gmail.com'], 
            [
                'name' => 'Client user',
                'password' => Hash::make('12345678'), 
                'role' => 'user',
            ]
        );

     
    }
}
