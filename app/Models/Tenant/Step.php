<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Step extends Model
{
    use HasFactory, SoftDeletes;

    public function activities()
    {
        return $this->hasMany(Activity::class, 'step_id');
    }

    public function schema()
    {
        return $this->hasOne(Schema::class, 'step_id');
    }

    public function model()
    {
        return $this->belongsTo(ModelType::class, 'model_id');
    }
}
