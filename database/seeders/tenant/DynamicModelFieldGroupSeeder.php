<?php

namespace Database\Seeders\tenant;

use App\Models\DynamicModelFieldGroup;
use Illuminate\Database\Seeder;

class DynamicModelFieldGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DynamicModelFieldGroup::insert([
            ['name' => 'Personal Information'],
            ['name' => 'Contact Details'],
            ['name' => 'Home Address'],
            ['name' => 'Work Address'],
            ['name' => 'Company Information'],
            ['name' => 'Company Address'],
        ]);
    }
}
