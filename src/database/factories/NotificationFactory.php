<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Campaign;
use App\Models\User;

class NotificationFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'user_id' => User::factory(),
      'campaign_id' => Campaign::factory(),
      'title' => $this->faker->sentence(5),
      'message' => $this->faker->paragraph(3),
      'read_at' => null,
    ];
  }
}
