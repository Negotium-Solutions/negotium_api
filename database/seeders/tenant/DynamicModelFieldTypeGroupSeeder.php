<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\DynamicModelFieldTypeGroup;
use Illuminate\Database\Seeder;

class DynamicModelFieldTypeGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DynamicModelFieldTypeGroup::insert([
            ['name' => 'User Input'],
            ['name' => 'Select'],
            ['name' => 'Uploads'],
            ['name' => 'Other']
        ]);
    }
}
