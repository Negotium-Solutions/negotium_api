<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DynamicModelFieldOption extends Model
{
    use HasFactory, SoftDeletes;

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'dynamic_model_field_id'
    ];
}
