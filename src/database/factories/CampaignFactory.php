<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class CampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title'      => $this->faker->sentence(5),
            'body'       => $this->faker->paragraph(3),
            'send_at'    => $this->faker->dateTimeBetween('now', '+7 days'),
            'status'     => $this->faker->randomElement(['draft', 'scheduled', 'processing', 'sent', 'failed']),
            'created_by' => optional(User::where('role', 'admin')->inRandomOrder()->first())->id,
        ];
    }
}
