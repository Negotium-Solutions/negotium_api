<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\DynamicModelFieldGroup;
use App\Models\Tenant\Schema as TenantSchema;
use Illuminate\Database\Seeder;

class DynamicModelFieldGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed for processes only, dynamic_model_type_id = 2
        $schemas = TenantSchema::where('dynamic_model_type_id', 2)->get();
        foreach ($schemas as $schema) {
            for($i = 1; $i <= 4; $i++) {
                $dynamicModelFieldGroup = new DynamicModelFieldGroup();
                $dynamicModelFieldGroup->name = 'Step 0'.$i;
                $dynamicModelFieldGroup->schema_id = $schema->id;
                $dynamicModelFieldGroup->save();
            }
        }
    }
}
