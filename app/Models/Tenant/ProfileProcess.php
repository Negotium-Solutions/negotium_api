<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfileProcess extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function processes() : HasMany
    {
        return $this->hasMany(DynamicModel::class, 'process_id', 'id');
    }
}
