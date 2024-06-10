<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityGroup extends Model
{
    use HasFactory, SoftDeletes;

    public function activity_types()
    {
        return $this->hasMany(ActivityType::class, 'activity_group_id');
    }
}
