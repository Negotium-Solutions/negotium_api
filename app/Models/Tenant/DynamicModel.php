<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;

class DynamicModel extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table; // This is set dynamically by the model using it [Profile, Process, etc]

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    const EMAIL = 13;

    /*
     * Transformed model properties
     */
    public function propertiesByGroup()
    {
        $properties = parent::toArray();

        $dynamicModelFieldGroups = DynamicModelFieldGroup::with(['fields.options', 'fields.validations'])->where('schema_id', $this->schema()->id)->get();

        foreach ($dynamicModelFieldGroups as $dynamicModelFieldGroup) {
            foreach ($dynamicModelFieldGroup->fields as $dynamicModelField) {
                if (self::EMAIL === $dynamicModelField->dynamic_model_field_type_id) {
                    $dynamicModelField->value = DynamicModelFieldEmail::find($properties[$dynamicModelField->field]);
                } else {
                    $dynamicModelField->value = $properties[$dynamicModelField->field];
                }
            }
        }

        return $dynamicModelFieldGroups;
    }

    public function propertiesByStep($parent_id)
    {
        $properties = parent::toArray();

        $dynamicModelFieldSteps = Step::with(['fields.options', 'fields.validations'])->where('parent_id', $parent_id)->get();

        foreach ($dynamicModelFieldSteps as $dynamicModelFieldGroup) {
            foreach ($dynamicModelFieldGroup->fields as $dynamicModelField) {
                if (self::EMAIL === $dynamicModelField->dynamic_model_field_type_id) {
                    $dynamicModelField->value = DynamicModelFieldEmail::find($properties[$dynamicModelField->field]);
                } else {
                    $dynamicModelField->value = $properties[$dynamicModelField->field];
                }
            }
        }

        return $dynamicModelFieldSteps;
    }

    public function steps() : HasMany
    {
        return $this->hasMany(Step::class, 'parent_id');
    }

    public function schema() : Model
    {
        return Schema::where('name', $this->table)->first();
    }

    public function createDynamicModel($name, $dynamic_model_category_id, $dynamic_model_type_id, $dynamic_model_template_id, $quick_capture)
    {
        $modelType = DynamicModelType::find($dynamic_model_type_id);
        $this->save();
        $this->name = $name;
        $this->schema_table_name = strtolower(str_replace(' ', '_', trim($modelType->name).'_'.$this->id));
        $this->dynamic_model_category_id = $dynamic_model_category_id;
        $this->dynamic_model_type_id = $dynamic_model_type_id;
        $this->dynamic_model_template_id = $dynamic_model_template_id;
        $this->quick_capture = $quick_capture;
        $this->save();

        \Illuminate\Support\Facades\Schema::create($this->schema_table_name, function (Blueprint $table) use ($dynamic_model_type_id) {
            $table->uuid('id')->primary();
            if ($dynamic_model_type_id === 2) {
                $table->uuid('profile_id')->nullable();
            }
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
