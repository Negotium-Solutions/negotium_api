<?php

namespace Database\Seeders;

use App\Models\Tenant\ModelType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModelTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ModelType::insert([
            ['name' => 'Process'],
            ['name' => 'ProfileType']
        ]);
    }
}
