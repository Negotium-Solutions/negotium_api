<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\Validation;
use App\Models\Tenant\DynamicModelField;
use App\Models\Tenant\DynamicModelFieldValidation;
use App\Models\Tenant\DynamicModelFieldGroup;
use App\Models\Tenant\DynamicModelFieldOption;
use App\Models\Tenant\DynamicModelFieldType;
use App\Models\Tenant\Schema as TenantSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DynamicModelFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $individualDynamicModel = json_decode(file_get_contents('database/templates/profile/individual.json'));
        $this->seedFromJsonData($individualDynamicModel, 100);

        $businessDynamicModel = json_decode(file_get_contents('database/templates/profile/business.json'));
        $this->seedFromJsonData($businessDynamicModel, 200);
    }

    public function seedFromJsonData($dynamicModel, $hardcoded_value_for_demo) : void
    {
        $schema = new TenantSchema();
        $schema->setName($dynamicModel->table);
        $schema->dynamic_model_category_id = $hardcoded_value_for_demo;
        $schema->save();
        Schema::create($schema->name, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        foreach ($dynamicModel->fields as $group => $dynamicModelFields) {
            $dynamicModelFieldGroup = new DynamicModelFieldGroup();
            $dynamicModelFieldGroup->name = $group;
            $dynamicModelFieldGroup->schema_id = $schema->id;
            $dynamicModelFieldGroup->save();

            foreach ($dynamicModelFields as $field => $_dynamicModelField) {
                $dynamicModelField = new DynamicModelField();
                $dynamicModelField->setField($field, true);
                $dynamicModelField->dynamic_model_field_type_id = $_dynamicModelField->type_id;
                $dynamicModelField->dynamic_model_field_group_id = $dynamicModelFieldGroup->id;
                $dynamicModelField->save();
                $dynamicModelField->order = $dynamicModelField->id;
                $dynamicModelField->save();

                Schema::table($schema->name, function (Blueprint $table) use ($_dynamicModelField, $dynamicModelField) {
                    $dynamicModelFieldType = DynamicModelFieldType::find($_dynamicModelField->type_id);
                    $table->{$dynamicModelFieldType->data_type}($dynamicModelField->field)->nullable();
                });

                foreach ($_dynamicModelField->validations as $_validation) {
                    $validation = Validation::where('name', $_validation)->first();
                    $dynamicModelFieldValidation = new DynamicModelFieldValidation();
                    $dynamicModelFieldValidation->validation_id = $validation->id;
                    $dynamicModelFieldValidation->dynamic_model_field_id = $dynamicModelField->id;
                    $dynamicModelFieldValidation->save();
                }

                if(isset($_dynamicModelField->options)) {
                    foreach ($_dynamicModelField->options as $option) {
                        $dynamicModelFieldOption = new DynamicModelFieldOption();
                        $dynamicModelFieldOption->name = $option;
                        $dynamicModelFieldOption->dynamic_model_field_id = $dynamicModelField->id;
                        $dynamicModelFieldOption->save();
                    }
                }
            }
        }
    }
}
