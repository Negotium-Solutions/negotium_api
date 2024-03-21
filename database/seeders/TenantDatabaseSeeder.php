<?php

namespace Database\Seeders;

use Database\Seeders\tenant\ProcessCategorySeeder;
use Database\Seeders\tenant\ProcessSeeder;
use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($domain = 'negotium-solutions.co.za'): void
    {
        $this->call(UserSeeder::class, false, ['domain' => $domain]);
        $this->call(ProcessCategorySeeder::class);
        $this->call(ProcessSeeder::class);
    }
}
