<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DynamicModelSchema extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dynamic_model_schemas';
}
