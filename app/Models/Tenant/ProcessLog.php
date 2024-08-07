<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcessLog extends Model
{
    use HasFactory, SoftDeletes;

    public function process() : BelongsTo {
        return $this->belongsTo(Process::class);
    }

    public function profile() : BelongsTo {
        return $this->belongsTo(Profile::class);
    }

    public function step() : BelongsTo {
        return $this->belongsTo(Step::class);
    }
}
