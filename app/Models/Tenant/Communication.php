<?php

namespace App\Models\Tenant;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Communication extends Model
{
    use HasFactory, SoftDeletes;

    function communicationType() : BelongsTo
    {
        return $this->belongsTo(CommunicationType::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
