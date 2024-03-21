<?php

namespace Database\Seeders\tenant;

use App\Models\ProcessCategory;
use Illuminate\Database\Seeder;

class ProcessCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProcessCategory::factory(['name' => 'Category 01'])->create();
        ProcessCategory::factory(['name' => 'Category 02'])->create();
        ProcessCategory::factory(['name' => 'Category 04'])->create();
        ProcessCategory::factory(['name' => 'Category 04'])->create();
        ProcessCategory::factory(['name' => 'Category 05'])->create();
    }
}
