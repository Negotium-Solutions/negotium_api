<?php

namespace App\Models\Tenant;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Communication extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    const STATUS_READ = 1;
    const STATUS_UNREAD = 2;
    const STATUS_PENDING = 3;
    const STATUS_DRAFT = 4;
    const STATUS_SENT = 5;

    function communicationType() : BelongsTo
    {
        return $this->belongsTo(CommunicationType::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function status() : BelongsTo
    {
        return $this->belongsTo(CommunicationStatus::class);
    }

    public function communications() : HasMany
    {
        return $this->hasMany(Communication::class, 'parent_id');
    }
}
