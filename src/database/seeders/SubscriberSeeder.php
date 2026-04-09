<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class SubscriberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('subscribers')->insert([
            [
                'name' => 'Laron Windler',
                'email' => 'user@gmail.com',
                'user_id' => 64,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
