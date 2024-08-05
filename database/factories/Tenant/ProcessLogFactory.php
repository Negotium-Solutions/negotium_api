<?php

namespace Database\Factories\Tenant;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant\ProcessLog>
 */
class ProcessLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'process_id' => rand(1, 20),
            'profile_id' => rand(1, 10),
            'process_status_id' => rand(1, 7),
            'user_id' => rand(1, 5),
            'step_id' => rand(1, 10),
            'activity_id' => rand(1, 10),
            'created_at' => $this->faker->dateTimeBetween('-1 months', 'now'),
        ];
    }
}
