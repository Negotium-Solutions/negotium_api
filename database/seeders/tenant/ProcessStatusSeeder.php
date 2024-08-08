<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\ProcessStatus;
use Illuminate\Database\Seeder;

class ProcessStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProcessStatus::insert([
           ['name' => ProcessStatus::ASSIGNED, 'created_at' => now()],
           ['name' => ProcessStatus::ACTIVE, 'created_at' => now()],
           ['name' => ProcessStatus::COMPLETED, 'created_at' => now()],
           ['name' => ProcessStatus::STOPPED, 'created_at' => now()],
           ['name' => ProcessStatus::RESUMED, 'created_at' => now()],
           ['name' => ProcessStatus::ARCHIVED, 'created_at' => now()]
        ]);
    }
}
