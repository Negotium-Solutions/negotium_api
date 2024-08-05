<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcessStatus extends Model
{
    use HasFactory, SoftDeletes;

    const START_PROCESS = 'started';
    const OPEN_PROCESS = 'opened';
    const STOP_PROCESS = 'stopped';
    const PAUSE_PROCESS = 'paused';
    const RESUME_PROCESS = 'resumed';
    const COMPLETE_PROCESS = 'completed';
    const ARCHIVE_PROCESS = 'archived';
}
