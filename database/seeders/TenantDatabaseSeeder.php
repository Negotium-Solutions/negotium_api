<?php

namespace Database\Seeders;

use Database\Seeders\tenant\ActivityGroupSeeder;
use Database\Seeders\tenant\ActivitySeeder;
use Database\Seeders\tenant\ActivityTypeSeeder;
use Database\Seeders\tenant\AttributeSeeder;
use Database\Seeders\tenant\CommunicationSeeder;
use Database\Seeders\tenant\CommunicationStatusSeeder;
use Database\Seeders\tenant\CommunicationTypeSeeder;
use Database\Seeders\tenant\DocumentSeeder;
use Database\Seeders\tenant\DynamicModelFieldGroupSeeder;
use Database\Seeders\tenant\DynamicModelFieldSeeder;
use Database\Seeders\tenant\FormSeeder;
use Database\Seeders\tenant\DynamicModelSchemaSeeder;
use Database\Seeders\tenant\NoteSeeder;
use Database\Seeders\tenant\ProcessCategorySeeder;
use Database\Seeders\tenant\ProcessLogSeeder;
use Database\Seeders\tenant\ProcessSeeder;
use Database\Seeders\tenant\ProcessStatusSeeder;
use Database\Seeders\tenant\ProfileProcessSeeder;
use Database\Seeders\tenant\ProfileSeeder;
use Database\Seeders\tenant\ProfileTypeSeeder;
use Database\Seeders\tenant\SchemaSeeder;
use Database\Seeders\tenant\StepSeeder;
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
        $this->call(SchemaSeeder::class);
        $this->call(ProfileSeeder::class);
        $this->call(ProfileProcessSeeder::class);
        $this->call(ProfileTypeSeeder::class);
        $this->call(DocumentSeeder::class);
        $this->call(ActivityGroupSeeder::class);
        $this->call(AttributeSeeder::class);
        $this->call(ModelTypeSeeder::class);
        $this->call(StepSeeder::class);
        $this->call(ActivitySeeder::class);
        $this->call(FormSeeder::class);
        $this->call(ProcessStatusSeeder::class);
        $this->call(ProcessLogSeeder::class);
        $this->call(NoteSeeder::class);
        $this->call(CommunicationSeeder::class);
        $this->call(CommunicationTypeSeeder::class);
        $this->call(CommunicationStatusSeeder::class);
        $this->call(DynamicModelSchemaSeeder::class);
        $this->call(DynamicModelSeeder::class);
        $this->call(DynamicModelFieldSeeder::class);
        $this->call(DynamicModelFieldGroupSeeder::class);
    }
}
