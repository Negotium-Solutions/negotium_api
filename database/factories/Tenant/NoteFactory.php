<?php

namespace Database\Factories\Tenant;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant\Note>
 */
class NoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'subject' => fake()->subject,
            'note' => fake()->text,
            'reminder_datetime' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'profile_id' => rand(1, 25),
            'status_id' => rand(1, 2)
        ];
    }
}
