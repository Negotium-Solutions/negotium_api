<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DynamicModelFieldGroup extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(DynamicModelFieldGroup::class, 'parent_id');
    }

    public function fields() : HasMany
    {
        return $this->hasMany(DynamicModelField::class);
    }
}
