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
           ['name' => ProcessStatus::ASSIGNED_NAME, 'created_at' => now()],
           ['name' => ProcessStatus::ACTIVE_NAME, 'created_at' => now()],
           ['name' => ProcessStatus::COMPLETED_NAME, 'created_at' => now()],
           ['name' => ProcessStatus::STOPPED_NAME, 'created_at' => now()],
           ['name' => ProcessStatus::RESUMED_NAME, 'created_at' => now()],
           ['name' => ProcessStatus::ARCHIVED_NAME, 'created_at' => now()]
        ]);
    }
}
