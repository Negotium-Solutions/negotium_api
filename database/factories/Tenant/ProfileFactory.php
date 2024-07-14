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
                'profile_type_id' => $profileType
            ];
        }

        if($profileType === 2) {
            $factoryData = [
                'company_name' => fake()->company(),
                'profile_type_id' => $profileType
            ];
        }

        return $factoryData;
    }
}
