<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\Process;
use App\Models\Tenant\ProcessLog;
use App\Models\Tenant\ProfileProcess;
use Illuminate\Database\Seeder;

class ProcessLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profileProcesses = ProfileProcess::get();
        foreach ($profileProcesses as $profileProcess) {
            ProcessLog::factory()->create([
                'process_id' => $profileProcess->process_id,
                'profile_id' => $profileProcess->profile_id,
            ]);
        }
    }
}
