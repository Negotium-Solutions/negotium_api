<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\ActivityGroup;
use Illuminate\Database\Seeder;

class ActivityGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ActivityGroup::insert([
            ['name' => 'User Input'],
            ['name' => 'Select'],
            ['name' => 'Uploads']
        ]);
    }
}
