<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\ProcessLog;
use Illuminate\Database\Seeder;

class ProcessLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProcessLog::factory()->count(50)->create();

        ProcessLog::insert([
            ['process_id' => 2, 'profile_id' => 1, 'user_id' => 1, 'step_id' => 1, 'activity_id' => 12, 'process_status_id' => 6, 'created_at' => now()],
            ['process_id' => 3, 'profile_id' => 1, 'user_id' => 1, 'step_id' => 2, 'activity_id' => 24, 'process_status_id' => 6, 'created_at' => now()],
            ['process_id' => 5, 'profile_id' => 1, 'user_id' => 1, 'step_id' => 3, 'activity_id' => 25, 'process_status_id' => 6, 'created_at' => now()]
        ]);
    }
}
