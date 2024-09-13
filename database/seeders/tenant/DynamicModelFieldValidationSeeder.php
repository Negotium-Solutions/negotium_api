<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\DynamicModelFieldValidation;
use Illuminate\Database\Seeder;

class DynamicModelFieldValidationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DynamicModelFieldValidation::insert([
            ['attribute_id' => 1, 'dynamic_model_field_id' => 1],
            ['attribute_id' => 3, 'dynamic_model_field_id' => 1],
            ['attribute_id' => 1, 'dynamic_model_field_id' => 2],
            ['attribute_id' => 6, 'dynamic_model_field_id' => 2],
            // ['attribute_id' => 1, 'dynamic_model_field_id' => 3],
            // ['attribute_id' => 1, 'dynamic_model_field_id' => 4],
            // ['attribute_id' => 1, 'dynamic_model_field_id' => 5],
            // ['attribute_id' => 1, 'dynamic_model_field_id' => 6],
            // ['attribute_id' => 1, 'dynamic_model_field_id' => 7],
            // ['attribute_id' => 1, 'dynamic_model_field_id' => 8],
            // ['attribute_id' => 6, 'dynamic_model_field_id' => 8],
            // ['attribute_id' => 1, 'dynamic_model_field_id' => 9],
            // ['attribute_id' => 1, 'dynamic_model_field_id' => 10],
            // ['attribute_id' => 1, 'dynamic_model_field_id' => 11],
            // ['attribute_id' => 1, 'dynamic_model_field_id' => 12],
            // ['attribute_id' => 1, 'dynamic_model_field_id' => 13],
            // ['attribute_id' => 1, 'dynamic_model_field_id' => 20],
            // ['attribute_id' => 1, 'dynamic_model_field_id' => 21],
            // ['attribute_id' => 1, 'dynamic_model_field_id' => 27],
            // ['attribute_id' => 17, 'dynamic_model_field_id' => 3],
            // ['attribute_id' => 17, 'dynamic_model_field_id' => 4],
            // ['attribute_id' => 17, 'dynamic_model_field_id' => 5]
        ]);
    }
}
