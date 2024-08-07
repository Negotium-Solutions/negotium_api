<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcessStatus extends Model
{
    use HasFactory, SoftDeletes;

    const ASSIGNED = 'assigned';
    const ACTIVE = 'active';
    const COMPLETED = 'completed';
    const STOPPED = 'stopped';
    const RESUMED = 'resumed';
    const ARCHIVED = 'archived';
}
