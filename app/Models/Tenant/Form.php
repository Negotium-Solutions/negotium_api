<?php

namespace App\Models\Tenant;

use App\definitions\ModelTypeDefinitions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form extends Model
{
    use HasFactory, SoftDeletes;

    public function steps()
    {
        return $this->hasMany(Step::class, 'parent_id')->where('model_id', ModelTypeDefinitions::FORM);
    }
}
