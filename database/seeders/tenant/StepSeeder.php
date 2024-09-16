<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\Process;
use App\Models\Tenant\Step;
use Illuminate\Database\Seeder;

class StepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $processes = Process::get();
        foreach ($processes as $key => $process) {
            Step::insert([
                ['name' => 'Step 01', 'parent_id' => $process->id],
                ['name' => 'Step 02', 'parent_id' => $process->id],
                ['name' => 'Step 03', 'parent_id' => $process->id],
                ['name' => 'Step 04', 'parent_id' => $process->id],
            ]);
        }
    }
}
