<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcessStatus extends Model
{
    use HasFactory, SoftDeletes;

    const ASSIGNED = 1;
    const ACTIVE = 2;
    const COMPLETED = 3;
    const STOPPED = 4;
    const RESUMED = 5;
    const ARCHIVED = 6;

    const ASSIGNED_NAME = 'assigned';
    const ACTIVE_NAME = 'active';
    const COMPLETED_NAME = 'completed';
    const STOPPED_NAME = 'stopped';
    const RESUMED_NAME = 'resumed';
    const ARCHIVED_NAME = 'archived';
}
