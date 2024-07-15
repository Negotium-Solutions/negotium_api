<?php

namespace App\Models\Tenant;

use App\definitions\ModelTypeDefinitions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Process extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name'
    ];

    public function category()
    {
        return $this->belongsTo(ProcessCategory::class, 'process_category_id');
    }

    public function steps()
    {
        return $this->hasMany(Step::class, 'parent_id')->where('model_id', ModelTypeDefinitions::PROCESS);
    }

    public function profiles() {
        return $this->hasManyThrough(
            Profile::class,
            ProfileProcess::class,
            'process_id',
            'id',
            'id',
            'profile_id'
        );
    }
}
