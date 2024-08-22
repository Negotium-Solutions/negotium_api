<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\hasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommunicationType extends Model
{
    use HasFactory, SoftDeletes;

    public function communication() : hasOne
    {
        return $this->hasOne(Communication::class);
    }
}
