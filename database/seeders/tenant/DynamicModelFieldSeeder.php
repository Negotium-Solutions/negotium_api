<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\Attribute;
use App\Models\Tenant\DynamicModelField;
use App\Models\Tenant\DynamicModelFieldAttribute;
use App\Models\Tenant\DynamicModelFieldGroup;
use App\Models\Tenant\DynamicModelFieldOption;
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
        $schema = new TenantSchema();
        $schema->setName($individualDynamicModel->table);

        Schema::create($schema->name, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        foreach ($individualDynamicModel->fields as $group => $dynamicModelFields) {
            $dynamicModelFieldGroup = new DynamicModelFieldGroup();
            $dynamicModelFieldGroup->name = $group;
            $dynamicModelFieldGroup->schema_id = $schema->id;
            $dynamicModelFieldGroup->save();

            foreach ($dynamicModelFields as $field => $_dynamicModelField) {
                $dynamicModelField = new DynamicModelField();
                $dynamicModelField->setField($field);
                $dynamicModelField->dynamic_model_field_group_id = $dynamicModelFieldGroup->id;
                $dynamicModelField->save();

                Schema::table($schema->name, function (Blueprint $table) use ($_dynamicModelField, $dynamicModelField) {
                    $table->{$_dynamicModelField->type}($dynamicModelField->field)->nullable();
                });

                foreach ($_dynamicModelField->attributes as $_attribute) {
                    $attribute = Attribute::where('name', $_attribute)->first();
                    $dynamicModelFieldAttribute = new DynamicModelFieldAttribute();
                    $dynamicModelFieldAttribute->attribute_id = $attribute->id;
                    $dynamicModelFieldAttribute->dynamic_model_field_id = $dynamicModelField->id;
                    $dynamicModelFieldAttribute->save();
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

        $businessDynamicModel = json_decode(file_get_contents('database/templates/profile/business.json'));
        $schema = new TenantSchema();
        $schema->setName($businessDynamicModel->table);
        Schema::create($schema->name, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        foreach ($businessDynamicModel->fields as $group => $dynamicModelField) {
            $dynamicModelFieldGroup = new DynamicModelFieldGroup();
            $dynamicModelFieldGroup->name = $group;
            $dynamicModelFieldGroup->schema_id = $schema->id;
            $dynamicModelFieldGroup->save();

            foreach ($dynamicModelFields as $field => $_dynamicModelField) {
                $dynamicModelField = new DynamicModelField();
                $dynamicModelField->setField($field);
                $dynamicModelField->dynamic_model_field_group_id = $dynamicModelFieldGroup->id;
                $dynamicModelField->save();

                Schema::table($schema->name, function (Blueprint $table) use ($_dynamicModelField, $dynamicModelField) {
                    $table->{$_dynamicModelField->type}($dynamicModelField->field)->nullable();
                });

                foreach ($_dynamicModelField->attributes as $_attribute) {
                    $attribute = Attribute::where('name', $_attribute)->first();
                    $dynamicModelFieldAttribute = new DynamicModelFieldAttribute();
                    $dynamicModelFieldAttribute->attribute_id = $attribute->id;
                    $dynamicModelFieldAttribute->dynamic_model_field_id = $dynamicModelField->id;
                    $dynamicModelFieldAttribute->save();
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
