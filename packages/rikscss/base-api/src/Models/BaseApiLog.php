<?php

namespace Rikscss\BaseApi\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rikscss\BaseApi\Database\Factories\BaseApiFactory;

class BaseApiLog extends Model
{
    use HasUuids, HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'route', 'payload', 'response', 'old_value', 'new_value', 'message', 'code', 'is_error'];

    protected static function newFactory() : BaseApiFactory
    {
        return BaseApiFactory::new();
    }
}
