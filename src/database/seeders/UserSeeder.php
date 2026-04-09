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
        User::updateOrCreate(
            ['email' => 'user@gmail.com'], 
            [
                'name' => 'User 01',
                'password' => Hash::make('12345678'), 
                'role' => 'user',
            ]
        );
    }
}
