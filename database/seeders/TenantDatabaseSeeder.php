<?php

namespace Database\Seeders;

use App\Models\Tenant\DynamicModelCategory;
use Database\Seeders\tenant\CommunicationSeeder;
use Database\Seeders\tenant\CommunicationStatusSeeder;
use Database\Seeders\tenant\CommunicationTypeSeeder;
use Database\Seeders\tenant\DocumentSeeder;
use Database\Seeders\tenant\DynamicModelCategorySeeder;
use Database\Seeders\tenant\DynamicModelFieldSeeder;
use Database\Seeders\tenant\DynamicModelFieldTypeGroupSeeder;
use Database\Seeders\tenant\DynamicModelFieldTypeSeeder;
use Database\Seeders\tenant\DynamicModelSchemaSeeder;
use Database\Seeders\tenant\DynamicModelSeeder;
use Database\Seeders\tenant\DynamicModelTemplateSeeder;
use Database\Seeders\tenant\DynamicModelTypeSeeder;
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
use Database\Seeders\tenant\ValidationSeeder;
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
        $this->call(DynamicModelTypeSeeder::class);
        $this->call(ProcessSeeder::class);
        $this->call(DynamicModelFieldTypeSeeder::class);
        $this->call(ValidationSeeder::class);
        $this->call(DynamicModelFieldSeeder::class);
        $this->call(DynamicModelCategorySeeder::class);
        $this->call(DynamicModelTemplateSeeder::class);
        $this->call(ProfileSeeder::class);
        $this->call(ProfileProcessSeeder::class);
        $this->call(ProfileTypeSeeder::class);
        $this->call(DocumentSeeder::class);
        $this->call(DynamicModelFieldTypeGroupSeeder::class);
        $this->call(StepSeeder::class);
        // $this->call(ActivitySeeder::class);
        $this->call(ProcessStatusSeeder::class);
        $this->call(ProcessLogSeeder::class);
        $this->call(NoteSeeder::class);
        $this->call(CommunicationSeeder::class);
        $this->call(CommunicationTypeSeeder::class);
        $this->call(CommunicationStatusSeeder::class);
        // $this->call(FormSeeder::class);
        $this->call(SchemaSeeder::class);
        $this->call(DynamicModelSeeder::class);
        // $this->call(DynamicModelSchemaSeeder::class);
        // $this->call(DynamicModelFieldGroupSeeder::class);
        // $this->call(DynamicModelFieldValidationSeeder::class);
    }
}
