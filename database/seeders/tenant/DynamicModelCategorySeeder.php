<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\DynamicModelCategory;
use Illuminate\Database\Seeder;

class DynamicModelCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DynamicModelCategory::insert([
            ['name' => 'Individual', 'dynamic_model_type_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Business', 'dynamic_model_type_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Category 01', 'dynamic_model_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Category 02', 'dynamic_model_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Category 03', 'dynamic_model_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Category 04', 'dynamic_model_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Category 05', 'dynamic_model_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
