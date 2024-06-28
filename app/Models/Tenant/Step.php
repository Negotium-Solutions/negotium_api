<?php

namespace App\Models\Tenant;

use App\definitions\ModelTypeDefinitions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Step extends Model
{
    use HasFactory, SoftDeletes;

    public function process()
    {
        return $this->belongsTo(Process::class, 'parent_id');
    }

    public function client_type()
    {
        return $this->belongsTo(ClientType::class, 'parent_id');
    }

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
