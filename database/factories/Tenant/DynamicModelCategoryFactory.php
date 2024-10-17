<?php

namespace Database\Factories\Tenant;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class DynamicModelCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'color' => fake()->colorName,
            'dynamic_model_type_id' => rand(1, 2)
        ];
    }
}
