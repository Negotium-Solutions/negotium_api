<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\DynamicModelFieldType;
use Illuminate\Database\Seeder;

class DynamicModelFieldTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DynamicModelFieldType::insert([
           ['name' => 'Text', 'data_type' => 'string', 'dynamic_model_field_type_group_id' => 1],
           ['name' => 'Text Area', 'data_type' => 'text', 'dynamic_model_field_type_group_id' => 1],
           ['name' => 'Percentage', 'data_type' => 'double', 'dynamic_model_field_type_group_id' => 1],
           ['name' => 'Integer', 'data_type' => 'integer', 'dynamic_model_field_type_group_id' => 1],
           ['name' => 'Amount', 'data_type' => 'double', 'dynamic_model_field_type_group_id' => 1],
           ['name' => 'Date', 'data_type' => 'date', 'dynamic_model_field_type_group_id' => 1],
           ['name' => 'Radio', 'data_type' => 'string', 'dynamic_model_field_type_group_id' => 2],
           ['name' => 'Checkbox', 'data_type' => 'string', 'dynamic_model_field_type_group_id' => 2],
           ['name' => 'Dropdown', 'data_type' => 'string', 'dynamic_model_field_type_group_id' => 2],
           ['name' => 'Documents', 'data_type' => 'string', 'dynamic_model_field_type_group_id' => 3],
           ['name' => 'Video', 'data_type' => 'string', 'dynamic_model_field_type_group_id' => 3],
           ['name' => 'Images', 'data_type' => 'string', 'dynamic_model_field_type_group_id' => 3],
           ['name' => 'Email Template', 'data_type' => 'string', 'dynamic_model_field_type_group_id' => 4],
           ['name' => 'Notification', 'data_type' => 'string', 'dynamic_model_field_type_group_id' => 4],
           ['name' => 'Link', 'data_type' => 'string', 'dynamic_model_field_type_group_id' => 4]
        ]);
    }
}
