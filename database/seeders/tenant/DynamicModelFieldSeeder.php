<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\DynamicModelField;
use App\Models\Tenant\Schema;
use Illuminate\Database\Seeder;

class DynamicModelFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $individual = Schema::where('name', 'individual_1')->first();
        DynamicModelField::insert([
           ['label' => 'ID Number', 'field' => 'id_number', 'schema_id' => $individual->id, 'dynamic_model_field_group_id' => 1],
           ['label' => 'Maiden Name', 'field' => 'maiden_name', 'schema_id' => $individual->id, 'dynamic_model_field_group_id' => 1],
           ['label' => 'Mobile Number', 'field' => 'mobile_number', 'schema_id' => $individual->id, 'dynamic_model_field_group_id' => 2],
           ['label' => 'Work Number', 'field' => 'work_number', 'schema_id' => $individual->id, 'dynamic_model_field_group_id' => 2],
           ['label' => 'Home Number', 'field' => 'home_number', 'schema_id' => $individual->id, 'dynamic_model_field_group_id' => 2],
           ['label' => 'Resident Type', 'field' => 'resident_type', 'schema_id' => $individual->id, 'dynamic_model_field_group_id' => 2],
           ['label' => 'Home Building Name', 'field' => 'home_building_name', 'schema_id' => $individual->id, 'dynamic_model_field_group_id' => 3],
           ['label' => 'Home Unit Number', 'field' => 'home_unit_number', 'schema_id' => $individual->id, 'dynamic_model_field_group_id' => 3],
           ['label' => 'Home Street name', 'field' => 'home_street_name', 'schema_id' => $individual->id, 'dynamic_model_field_group_id' => 3],
           ['label' => 'Home Suburb', 'field' => 'home_suburb', 'schema_id' => $individual->id, 'dynamic_model_field_group_id' => 3],
           ['label' => 'Home City', 'field' => 'home_city', 'schema_id' => $individual->id, 'dynamic_model_field_group_id' => 3],
           ['label' => 'Home Country', 'field' => 'home_country', 'schema_id' => $individual->id, 'dynamic_model_field_group_id' => 3],
           ['label' => 'Home Postal Code', 'field' => 'home_postal_code', 'schema_id' => $individual->id, 'dynamic_model_field_group_id' => 3],
           ['label' => 'Work Building Name', 'field' => 'work_building_name', 'schema_id' => $individual->id, 'dynamic_model_field_group_id' => 4],
           ['label' => 'Work Street Address', 'field' => 'work_street_address', 'schema_id' => $individual->id, 'dynamic_model_field_group_id' => 4],
           ['label' => 'Work Suburb', 'field' => 'work_suburb', 'schema_id' => $individual->id, 'dynamic_model_field_group_id' => 4],
           ['label' => 'Work City', 'field' => 'work_city', 'schema_id' => $individual->id, 'dynamic_model_field_group_id' => 4],
           ['label' => 'Work Country', 'field' => 'work_country', 'schema_id' => $individual->id, 'dynamic_model_field_group_id' => 4],
           ['label' => 'Work Postal Code', 'field' => 'work_postal_code', 'schema_id' => $individual->id, 'dynamic_model_field_group_id' => 4],
        ]);

        $business = Schema::where('name', 'business_2')->first();
        DynamicModelField::insert([
           ['label' => 'Company Registration', 'field' => 'company_registration_number', 'schema_id' => $business->id, 'dynamic_model_field_group_id' => 5],
           ['label' => 'Building Name', 'field' => 'building_name', 'schema_id' => $business->id, 'dynamic_model_field_group_id' => 6],
           ['label' => 'Unit Number', 'field' => 'unit_number', 'schema_id' => $business->id, 'dynamic_model_field_group_id' => 6],
           ['label' => 'Street Address', 'field' => 'street_address', 'schema_id' => $business->id, 'dynamic_model_field_group_id' => 6],
           ['label' => 'Suburb', 'field' => 'suburb', 'schema_id' => $business->id, 'dynamic_model_field_group_id' => 6],
           ['label' => 'City', 'field' => 'city', 'schema_id' => $business->id, 'dynamic_model_field_group_id' => 6],
           ['label' => 'Country', 'field' => 'country', 'schema_id' => $business->id, 'dynamic_model_field_group_id' => 6],
           ['label' => 'Postal Code', 'field' => 'postal_code', 'schema_id' => $business->id, 'dynamic_model_field_group_id' => 6],
        ]);
    }
}
