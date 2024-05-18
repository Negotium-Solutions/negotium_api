<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\Attribute;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Attribute::insert([
            ['name' => 'required', 'label' => 'Required'],
            ['name' => 'indent', 'label' => 'Indent'],
            ['name' => 'id_verification', 'label' => 'ID Verification'],
            ['name' => 'passport_verification', 'label' => 'Passport Verification'],
            ['name' => 'date', 'label' => 'Date'],
        ]);
    }
}
