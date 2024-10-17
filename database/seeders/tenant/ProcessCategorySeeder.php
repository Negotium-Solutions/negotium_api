<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\DynamicModelCategory;
use App\Models\Tenant\ProcessCategory;
use Illuminate\Database\Seeder;

class ProcessCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DynamicModelCategory::factory(['name' => 'Category 01', 'dynamic_model_type_id' => 2, 'color' => fake()->colorName])->create();
        DynamicModelCategory::factory(['name' => 'Category 02', 'dynamic_model_type_id' => 2, 'color' => fake()->colorName])->create();
        DynamicModelCategory::factory(['name' => 'Category 03', 'dynamic_model_type_id' => 2, 'color' => fake()->colorName])->create();
        DynamicModelCategory::factory(['name' => 'Category 04', 'dynamic_model_type_id' => 2, 'color' => fake()->colorName])->create();
        DynamicModelCategory::factory(['name' => 'Category 05', 'dynamic_model_type_id' => 2, 'color' => fake()->colorName])->create();
        DynamicModelCategory::factory(['name' => 'Category 06', 'dynamic_model_type_id' => 2, 'color' => fake()->colorName])->create();
        DynamicModelCategory::factory(['name' => 'Category 07', 'dynamic_model_type_id' => 2, 'color' => fake()->colorName])->create();
    }
}
