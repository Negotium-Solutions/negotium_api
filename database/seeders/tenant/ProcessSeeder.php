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
        // Seed  Dynamic Model Processes
        for($i = 1; $i <= 25; $i++) {
            $schema = new Schema();
            $schema->createDynamicModel('Process '.($i <= 9 ? '0': '').$i , rand(1, 5), 2, 1, 'Yes');
        }
    }
}
