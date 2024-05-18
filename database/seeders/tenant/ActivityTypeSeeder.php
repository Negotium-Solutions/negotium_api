<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\ActivityType;
use Illuminate\Database\Seeder;

class ActivityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ActivityType::insert([
           ['name' => 'Number', 'schema_data_type' => 'integer', 'activity_group_id' => 1],
           ['name' => 'Text', 'schema_data_type' => 'string', 'activity_group_id' => 1],
           ['name' => 'Date', 'schema_data_type' => 'date', 'activity_group_id' => 1],
           ['name' => 'Long Text', 'schema_data_type' => 'text', 'activity_group_id' => 1],
           ['name' => 'Radio', 'schema_data_type' => 'string', 'group_id' => 2],
           ['name' => 'Checkbox', 'schema_data_type' => 'string', 'group_id' => 2],
           ['name' => 'Dropdown', 'schema_data_type' => 'string', 'group_id' => 2],
           ['name' => 'Documents', 'schema_data_type' => 'string', 'group_id' => 3],
           ['name' => 'Video', 'schema_data_type' => 'string', 'group_id' => 3],
           ['name' => 'Images', 'schema_data_type' => 'string', 'group_id' => 3]
        ]);
    }
}
