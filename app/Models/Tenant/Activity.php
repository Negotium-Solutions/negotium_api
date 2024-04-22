<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    public function step()
    {
        return $this->belongsTo(ProcessStep::class);
    }

    public function schemas()
    {
        return $this->hasMany(Schema::class, 'parent_id');
    }
}
