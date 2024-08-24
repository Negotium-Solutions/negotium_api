<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\DynamicModelSchema;
use Illuminate\Database\Seeder;

class DynamicModelSchemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DynamicModelSchema::insert([
           ['dynamic_model_id' => 1, 'schema_id' => 1],
           ['dynamic_model_id' => 2, 'schema_id' => 2]
        ]);
    }
}
