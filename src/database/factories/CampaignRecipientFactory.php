<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Campaign;
use App\Models\Subscriber;

class CampaignRecipientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'campaign_id'   => Campaign::factory(),
            'subscriber_id' => Subscriber::factory(),
            'status'        => $this->faker->randomElement(['pending', 'sent', 'failed']),
            'sent_at'       => null,
        ];
    }
}
