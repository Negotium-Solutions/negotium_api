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
        $schema = new Schema();
        $schema->name = 'individual_1';
        $schema->save();

        $schema = new Schema();
        $schema->name = 'business_2';
        $schema->save();
    }
}
