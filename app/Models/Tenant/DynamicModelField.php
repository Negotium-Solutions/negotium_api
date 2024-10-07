<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DynamicModelField extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function step() : BelongsTo
    {
        return $this->belongsTo(Step::class);
    }

    public function group() : BelongsTo
    {
        return $this->belongsTo(DynamicModelFieldGroup::class, 'dynamic_model_field_group_id');
    }

    public function field_type() : BelongsTo
    {
        return $this->belongsTo(DynamicModelFieldType::class, 'dynamic_model_field_type_id');
    }

    public function validations() : HasManyThrough
    {
        return $this->hasManyThrough(
            Validation::class,
            DynamicModelFieldValidation::class,
            'dynamic_model_field_id',
            'id',
            'id',
            'validation_id'
        );
    }

    public function options() : HasMany
    {
        return $this->hasMany(DynamicModelFieldOption::class, 'dynamic_model_field_id');
    }

    public function setField($field, $defaultProfile = false) : void
    {
        $this->save();
        $this->label = $field;
        if(in_array($field, ['First Name', 'Last Name', 'Company Name', 'Email', 'Cell Number']) && $defaultProfile) {
            $this->field = trim(str_replace(' ', '_', strtolower($field)));
        } else {
            $this->field = trim(str_replace(' ', '_', strtolower($field))).'_'.$this->id;
        }
        $this->save();
    }

    public function createFields($schema_table_name, $step_id, $dynamicModelFields)
    {
        foreach ($dynamicModelFields as $__dynamicModelFields) {
            foreach ($__dynamicModelFields as $field => $_dynamicModelField) {
                $dynamicModelField = new DynamicModelField();
                $dynamicModelField->setField($field, true);
                $dynamicModelField->dynamic_model_field_type_id = $_dynamicModelField->type_id;
                $dynamicModelField->step_id = $step_id;
                $dynamicModelField->save();
                $dynamicModelField->order = $dynamicModelField->id;
                $dynamicModelField->save();

                Schema::table($schema_table_name, function (Blueprint $table) use ($_dynamicModelField, $dynamicModelField) {
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
}
