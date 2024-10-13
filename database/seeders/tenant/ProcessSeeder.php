<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\Process;
use App\Models\Tenant\Schema;
use App\Models\Tenant\Schema as TenantSchema;
use Illuminate\Database\Seeder;

class ProcessSeeder extends Seeder
{
    const PROCESS_KEY = 'process';
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
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

        $processes = Process::all();

        $processes->each(function (Process $process) {
            $schema = new TenantSchema();
            $schema->createSchema(self::PROCESS_KEY);
            $process->schema_id = $schema->id;
            $process->save();
        });
        */

        // Seed  Dynamic Model Processes
        for($i = 1; $i <= 25; $i++) {
            $schema = new Schema();
            $schema->createDynamicModel('Process '.$i , rand(1, 5), 2, 1, 'Yes');
        }
    }
}
