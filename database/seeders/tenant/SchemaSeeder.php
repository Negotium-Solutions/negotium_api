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
        $schema->createDynamicModel('Capture individual details', 1, 1, 1, 'Yes');

        $schema = new Schema();
        $schema->createDynamicModel('Capture business details', 2, 1, 2, 'Yes');

    }
}
