<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class DynamicModel extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table; // This is set dynamically by the model using it [Profile, Process, etc]

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /*
     * Transformed model properties
     */
    public function propertiesByGroup()
    {
        $properties = parent::toArray();

        $dynamicModelFieldGroups = DynamicModelFieldGroup::with(['fields.options', 'fields.validations'])->where('schema_id', $this->schema()->id)->get();

        foreach ($dynamicModelFieldGroups as $dynamicModelFieldGroup) {
            foreach ($dynamicModelFieldGroup->fields as $dynamicModelField) {
                $dynamicModelField->value = $properties[$dynamicModelField->field];
            }
        }

        return $dynamicModelFieldGroups;
    }

    public function schema() : Model
    {
        return Schema::where('name', $this->table)->first();
    }
}
