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

    public function dynamicModelFieldGroup() : BelongsTo
    {
        return $this->belongsTo(DynamicModelFieldGroup::class);
    }

    public function transformed()
    {
        $properties = parent::toArray();

        $dynamicModelFieldGroups = DynamicModelFieldGroup::with(['dynamicModelFields'])->where('schema_id', $this->schema()->id)->get();

        foreach ($dynamicModelFieldGroups as $dynamicModelFieldGroup) {
            foreach ($dynamicModelFieldGroup->dynamicModelFields as $key => $dynamicModelField) {
                $dynamicModelField->value = $properties[$dynamicModelField->field];
                $dynamicModelField->dynamic_model_field_attributes = !empty($dynamicModelField->dynamicModelFieldAttributes) ? $dynamicModelField->dynamicModelFieldAttributes : [];
            }
        }

        return $dynamicModelFieldGroups;
    }

    public function getDynamicModelFieldId($columnName)
    {
        $comment = DB::table('information_schema.COLUMNS')
                ->where('TABLE_NAME', $this->table)
                ->where('COLUMN_NAME', $columnName)
                ->value('COLUMN_COMMENT');

        return isset(json_decode($comment)->dynamic_model_field_id) ? json_decode($comment)->dynamic_model_field_id : null;
    }

    public function schema() : Model
    {
        return Schema::where('name', $this->table)->first();
    }

    public function addColumn()
    {

    }
}
