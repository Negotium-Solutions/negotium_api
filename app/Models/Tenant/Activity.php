<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function step()
    {
        return $this->belongsTo(Step::class);
    }

    public function type()
    {
        return $this->belongsTo(DynamicModelFieldType::class);
    }
}
