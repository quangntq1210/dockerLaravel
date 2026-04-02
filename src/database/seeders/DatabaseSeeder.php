<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\AdminSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\SubscriberSeeder;
use Database\Seeders\CampaignSeeder;
use Database\Seeders\NotificationSeeder;
use Database\Seeders\CampaignRecipientSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AdminSeeder::class,
            UserSeeder::class,
            SubscriberSeeder::class,
            CampaignSeeder::class,
            NotificationSeeder::class,
            CampaignRecipientSeeder::class,
        ]);
    }
}
