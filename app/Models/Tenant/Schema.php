<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Schema extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $hidden = [
        'deleted_at'
    ];

    protected $appends = [
        'has_data'
    ];

    public function getColumns()
    {
        $raw_columns = DB::select("SELECT column_name as name, column_type as type, column_comment as attributes FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$this->name."'");

        $columns = [];
        foreach ($raw_columns as $key => $column) {
            $attributes = json_decode($column->attributes, true);
            if(!(isset($attributes['deleted_at']) && $attributes['deleted_at'] != '')) {
                $columns[$key] = [
                    'name' => $column->name,
                    'type' => $column->type,
                    'attributes' => $attributes
                ];
            }
        }

        return $columns;
    }

    public function setName($name) : void
    {
        $this->save();
        $this->name = $name.'_'.$this->id;
        $this->save();
    }

    public function createSchema($name)
    {
        $this->save();
        $this->name = $name.'_'.$this->id;
        $this->save();

        \Illuminate\Support\Facades\Schema::create($this->name, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function createField($_table, $name, $type)
    {
        \Illuminate\Support\Facades\Schema::table($_table, function (Blueprint $table) use ($name, $type) {
            $table->{$type}($name)->nullable();
        });
    }

    public function createDynamicModel($name, $dynamic_model_category_id, $dynamic_model_type_id, $quick_capture)
    {
        $modelType = DynamicModelType::find($dynamic_model_type_id);
        $this->save();
        $this->name = $name;
        $this->table_name = strtolower(str_replace(' ', '_', trim($modelType->name).'_'.$this->id));
        $this->dynamic_model_category_id = $dynamic_model_category_id;
        $this->dynamic_model_type_id = $dynamic_model_type_id;
        $this->quick_capture = $quick_capture;
        $this->save();

        \Illuminate\Support\Facades\Schema::create($this->table_name, function (Blueprint $table) use ($dynamic_model_type_id, $dynamic_model_category_id) {
            $table->uuid('id')->primary();
            $table->uuid('schema_id')->nullable();
            $table->uuid('parent_id')->nullable();
            switch($dynamic_model_type_id) {
                case DynamicModelType::PROFILE:
                    $table->string('avatar')->nullable();
                    break;
                case DynamicModelType::PROCESS:
                    $table->uuid('name')->nullable();
                    $table->uuid('profile_id')->nullable();
                    break;
            }
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function createDynamicModelFields($schema, $dynamicModelFields, $defaultProfile = false)
    {
        foreach ($dynamicModelFields as $step_name => $_step) {
            /*
            $step = new Step();
            $step->name = $step_name;
            $step->parent_id = $schema->id;
            $step->save();
            $step->order = $step->id;
            $step->save();
            */

            $dynamicModelFieldGroup = new DynamicModelFieldGroup();
            $dynamicModelFieldGroup->name = $step_name;
            $dynamicModelFieldGroup->schema_id = $schema->id;
            $dynamicModelFieldGroup->save();
            $dynamicModelFieldGroup->order = (DynamicModelFieldGroup::all()->count() + 1) * 10;
            $dynamicModelFieldGroup->save();

            foreach ($_step as $field => $_dynamicModelField) {
                $dynamicModelField = new DynamicModelField();
                $dynamicModelField->setField($field, $defaultProfile);
                $dynamicModelField->dynamic_model_field_type_id = $_dynamicModelField->type_id;
                // $dynamicModelField->step_id = $step->id;
                $dynamicModelField->dynamic_model_field_group_id = $dynamicModelFieldGroup->id;
                $dynamicModelField->save();
                $dynamicModelField->order = $dynamicModelField->id;
                $dynamicModelField->save();

                \Illuminate\Support\Facades\Schema::table($schema->table_name, function (Blueprint $table) use ($_dynamicModelField, $dynamicModelField) {
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

                if (isset($_dynamicModelField->options)) {
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

    // New Code
    public function createColumn($name, $type)
    {
        \Illuminate\Support\Facades\Schema::table($this->table_name, function (Blueprint $table) use ($name, $type) {
            $table->{$type}($name)->nullable();
        });
    }

    public function getHasDataAttribute()
    {
        return $this->table_name;
        // return $this->table_name::query()->qet()->count() > 0 ? true : false;
    }

    public function models() : HasMany
    {
        return $this->HasMany(DynamicModel::class, 'schema_id');
    }

    public function steps() : HasMany
    {
        return $this->hasMany(Step::class, 'parent_id');
    }

    public function groups() : HasMany
    {
        return $this->hasMany(DynamicModelFieldGroup::class, 'schema_id');
    }
}
