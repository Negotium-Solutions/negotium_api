<?php

namespace App\Models\Tenant;

use App\Models\DynamicModelFieldGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DynamicModelField extends Model
{
    use HasFactory;

    public function schema() : BelongsTo
    {
        return $this->belongsTo(Schema::class, 'schema_id');
    }

    public function dynamicModelFieldGroup()
    {
        return $this->belongsTo(DynamicModelFieldGroup::class, 'dynamic_model_field_group_id');
    }
}
