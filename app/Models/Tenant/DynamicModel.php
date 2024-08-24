<?php

namespace App\Models\Tenant;

use App\Models\DynamicModelFieldGroup;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DynamicModel extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table; // This is set dynamically by the model using it [Profile, Process, etc]

    public function dynamicModelFieldGroup() : BelongsTo
    {
        return $this->belongsTo(DynamicModelFieldGroup::class);
    }
}
