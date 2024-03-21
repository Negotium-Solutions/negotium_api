<?php

namespace Database\Seeders\tenant;

use App\Models\Process;
use Illuminate\Database\Seeder;

class ProcessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Process::factory(['name' => 'Process 01'])->create();
        Process::factory(['name' => 'Process 02'])->create();
        Process::factory(['name' => 'Process 03'])->create();
        Process::factory(['name' => 'Process 04'])->create();
        Process::factory(['name' => 'Process 05'])->create();
        Process::factory(['name' => 'Process 06'])->create();
        Process::factory(['name' => 'Process 07'])->create();
        Process::factory(['name' => 'Process 08'])->create();
        Process::factory(['name' => 'Process 09'])->create();
        Process::factory(['name' => 'Process 10'])->create();
        Process::factory(['name' => 'Process 11'])->create();
        Process::factory(['name' => 'Process 12'])->create();
        Process::factory(['name' => 'Process 13'])->create();
        Process::factory(['name' => 'Process 14'])->create();
        Process::factory(['name' => 'Process 15'])->create();
        Process::factory(['name' => 'Process 16'])->create();
        Process::factory(['name' => 'Process 17'])->create();
        Process::factory(['name' => 'Process 18'])->create();
        Process::factory(['name' => 'Process 19'])->create();
        Process::factory(['name' => 'Process 20'])->create();
        Process::factory(['name' => 'Process 21'])->create();
        Process::factory(['name' => 'Process 22'])->create();
        Process::factory(['name' => 'Process 23'])->create();
        Process::factory(['name' => 'Process 24'])->create();
        Process::factory(['name' => 'Process 25'])->create();
    }
}
