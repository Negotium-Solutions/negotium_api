<?php

namespace Rikscss\BaseApi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseApiLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'route', 'payload', 'response', 'old_value', 'new_value', 'message', 'code', 'is_error'];
}
