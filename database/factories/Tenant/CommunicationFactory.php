<?php

namespace Database\Factories\Tenant;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant\Communication>
 */
class CommunicationFactory extends Factory
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
            'profile_id' => rand(1, 22),
            'user_id' => $userIds[rand(0, count($userIds) - 1)],
            'communication_type_id' => rand(1, 4)
        ];
    }
}
