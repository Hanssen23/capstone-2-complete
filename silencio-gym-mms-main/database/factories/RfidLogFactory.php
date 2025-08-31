<?php

namespace Database\Factories;

use App\Models\RfidLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RfidLog>
 */
class RfidLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $actions = ['check_in', 'check_out', 'unknown_card', 'expired_membership'];
        $statuses = ['success', 'failed'];
        
        return [
            'card_uid' => $this->faker->regexify('[A-Z0-9]{8}'),
            'action' => $this->faker->randomElement($actions),
            'status' => $this->faker->randomElement($statuses),
            'message' => $this->faker->sentence(),
            'timestamp' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'device_id' => 'main_reader',
        ];
    }
}

