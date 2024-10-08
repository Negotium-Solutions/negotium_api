<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\DynamicModelType;
use Illuminate\Database\Seeder;

class DynamicModelTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DynamicModelType::insert([
            ['name' => 'Profile'],
            ['name' => 'Process']
        ]);
    }
}
