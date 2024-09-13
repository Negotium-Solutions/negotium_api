<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class DynamicModelField extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function schema() : BelongsTo
    {
        return $this->belongsTo(Schema::class, 'schema_id');
    }

    public function group() : BelongsTo
    {
        return $this->belongsTo(DynamicModelFieldGroup::class, 'dynamic_model_field_group_id');
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

    public function setField($field) : void
    {
        $this->save();
        $this->label = $field;
        $this->field = trim(str_replace(' ', '_', strtolower($field))).'_'.$this->id;
        $this->save();
    }
}
