<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\definitions\ModelTypeDefinitions;

class Process extends Model
{
    use HasFactory;

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
}
