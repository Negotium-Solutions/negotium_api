<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\DynamicModelFieldGroup;
use App\Models\Tenant\Schema;
use Illuminate\Database\Seeder;

class DynamicModelFieldGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $individual = Schema::where('name', 'individual_1')->first();
        $business = Schema::where('name', 'business_2')->first();

        DynamicModelFieldGroup::insert([
            ['name' => 'Personal Information', 'schema_id' => $individual->id],
            ['name' => 'Contact Details', 'schema_id' => $individual->id],
            ['name' => 'Home Address', 'schema_id' => $individual->id],
            ['name' => 'Work Address', 'schema_id' => $individual->id],
            ['name' => 'Company Information', 'schema_id' => $business->id],
            ['name' => 'Company Address', 'schema_id' => $business->id],
        ]);
    }
}
