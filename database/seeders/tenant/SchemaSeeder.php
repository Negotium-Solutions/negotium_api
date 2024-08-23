<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\Schema;
use Illuminate\Database\Seeder;

class SchemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::insert([
           ['name' => 'individual_1'],
           ['name' => 'business_2']
        ]);
    }
}
