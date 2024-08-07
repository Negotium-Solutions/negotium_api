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
        for($counter = 1; $counter <= 22; $counter++) {
            ProcessLog::factory()->create([
                'process_id' => $counter,
                'profile_id' => $counter,
            ]);
        }
    }
}
