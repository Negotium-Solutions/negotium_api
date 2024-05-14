<?php

namespace Database\Seeders;

use App\Models\Tenant\ClientType;
use Database\Seeders\tenant\ClientSeeder;
use Database\Seeders\tenant\ClientTypeSeeder;
use Database\Seeders\tenant\DocumentSeeder;
use Database\Seeders\tenant\ProcessCategorySeeder;
use Database\Seeders\tenant\ProcessSeeder;
use Database\Seeders\tenant\ProcessStepSeeder;
use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($domain = 'negotium-solutions.com'): void
    {
        $this->call(UserSeeder::class, false, ['domain' => $domain]);
        $this->call(ProcessCategorySeeder::class);
        $this->call(ProcessSeeder::class);
        $this->call(ProcessStepSeeder::class);
        $this->call(ClientSeeder::class);
        $this->call(ClientTypeSeeder::class);
        $this->call(DocumentSeeder::class);
    }
}
