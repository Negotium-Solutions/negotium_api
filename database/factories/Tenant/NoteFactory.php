<?php

namespace Database\Factories\Tenant;

use App\Models\User;
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
        $userIds = User::orderBy('id')->pluck('id')->toArray();

        return [
            'subject' => fake()->realTextBetween(5, 15),
            'note' => fake()->realText,
            'reminder_datetime' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'profile_id' => rand(1, 22),
            'user_id' => $userIds[rand(0, count($userIds) - 1)]
        ];
    }
}
