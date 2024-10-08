<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DynamicModelType extends Model
{
    const PROFILE = 1;
    const PROCESS = 2;

    use HasFactory, SoftDeletes;
}
