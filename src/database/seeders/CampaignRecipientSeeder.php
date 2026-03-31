<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CampaignRecipient;

class CampaignRecipientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CampaignRecipient::factory(50)->create();
    }
}
