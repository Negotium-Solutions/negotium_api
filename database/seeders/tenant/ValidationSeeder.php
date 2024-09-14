<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\Validation;
use Illuminate\Database\Seeder;

class ValidationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Validation::insert([
            ['name' => 'required', 'label' => 'Required'],
            ['name' => 'indent', 'label' => 'Indent'],
            ['name' => 'sa_id_number', 'label' => 'SA ID Number'],
            ['name' => 'sa_passport_number', 'label' => 'SA Passport Number'],
            ['name' => 'date', 'label' => 'Date'],
            ['name' => 'string', 'label' => 'String'],
            ['name' => 'integer', 'label' => 'Integer'],
            ['name' => 'email', 'label' => 'Email'],
            ['name' => 'numeric', 'label' => 'Numeric'],
            ['name' => 'array', 'label' => 'Array'],
            ['name' => 'image', 'label' => 'Image'],
            ['name' => 'file', 'label' => 'File'],
            ['name' => 'url', 'label' => 'URL'],
            ['name' => 'uuid', 'label' => 'UUID'],
            ['name' => 'json', 'label' => 'Json'],
            ['name' => 'boolean', 'label' => 'Boolean'],
            ['name' => 'sa_phone_number', 'label' => 'SA Phone Number']
        ]);
    }
}
