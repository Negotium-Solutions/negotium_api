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
        $data = json_decode(file_get_contents(base_path('database/templates/profile/personal_information.json')));
        $schema->createDynamicModelFields($schema, $data, true);

        $data = json_decode(file_get_contents(base_path('database/templates/profile/personal_contact_information.json')));
        $schema->createDynamicModelFields($schema, $data, true);

        $schema = new Schema();
        $schema->createDynamicModel('Capture business details', 2, 1, 2, 'Yes');
        $data = json_decode(file_get_contents(base_path('database/templates/profile/company_information.json')));
        $schema->createDynamicModelFields($schema, $data, true);

        $data = json_decode(file_get_contents(base_path('database/templates/profile/business_contact_information.json')));
        $schema->createDynamicModelFields($schema, $data, true);
    }
}
