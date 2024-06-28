<?php

namespace Database\Seeders;

use Database\Seeders\tenant\ActivityGroupSeeder;
use Database\Seeders\tenant\ActivitySeeder;
use Database\Seeders\tenant\ActivityTypeSeeder;
use Database\Seeders\tenant\AttributeSeeder;
use Database\Seeders\tenant\ClientSeeder;
use Database\Seeders\tenant\ClientTypeSeeder;
use Database\Seeders\tenant\DocumentSeeder;
use Database\Seeders\tenant\FormSeeder;
use Database\Seeders\tenant\ProcessCategorySeeder;
use Database\Seeders\tenant\ProcessSeeder;
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
        $this->call(ActivityTypeSeeder::class);
        $this->call(ClientSeeder::class);
        $this->call(ClientTypeSeeder::class);
        $this->call(DocumentSeeder::class);
        $this->call(ActivityGroupSeeder::class);
        $this->call(AttributeSeeder::class);
        $this->call(ModelTypeSeeder::class);
        $this->call(StepSeeder::class);
        $this->call(ActivitySeeder::class);
        $this->call(FormSeeder::class);
    }
}
