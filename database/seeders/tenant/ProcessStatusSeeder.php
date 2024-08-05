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
           ['name' => ProcessStatus::START_PROCESS, 'created_at' => now()],
           ['name' => ProcessStatus::OPEN_PROCESS, 'created_at' => now()],
           ['name' => ProcessStatus::PAUSE_PROCESS, 'created_at' => now()],
           ['name' => ProcessStatus::STOP_PROCESS, 'created_at' => now()],
           ['name' => ProcessStatus::RESUME_PROCESS, 'created_at' => now()],
           ['name' => ProcessStatus::COMPLETE_PROCESS, 'created_at' => now()],
           ['name' => ProcessStatus::ARCHIVE_PROCESS, 'created_at' => now()],
        ]);
    }
}
