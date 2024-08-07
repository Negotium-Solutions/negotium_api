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
            'process_id' => rand(1, 25),
            'profile_id' => rand(1, 10),
            'process_status_id' => rand(1, 6),
            'step_id' => rand(1, 10),
            'created_at' => $this->faker->dateTimeBetween('-6 months', '-2 months'),
            'updated_at' => $this->faker->dateTimeBetween('-2 months', 'now'),
        ];
    }
}
