<?php

namespace Database\Factories\Tenant;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $profileType = rand(1, 2);

        if($profileType === 1) {
            $factoryData = [
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => fake()->email(),
                'profile_type_id' => $profileType,
                'avatar' => '/images/individual/avatar'.rand(1, 5).'.png'
            ];
        }

        if($profileType === 2) {
            $factoryData = [
                'company_name' => fake()->company(),
                'email' => fake()->email(),
                'profile_type_id' => $profileType,
                'avatar' => '/images/business/avatar'.rand(1, 5).'.png'
            ];
        }

        return $factoryData;
    }
}
