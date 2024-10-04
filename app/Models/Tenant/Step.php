<?php

namespace App\Models\Tenant;

use App\definitions\ModelTypeDefinitions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Step extends Model
{
    use HasFactory, SoftDeletes;

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function process()
    {
        return $this->belongsTo(Process::class, 'parent_id');
    }

    public function profile_type()
    {
        return $this->belongsTo(ProfileType::class, 'parent_id');
    }

    public function activities()
    {
        return $this->hasMany(DynamicModelField::class, 'step_id');
    }

    public function schema()
    {
        return $this->hasOne(Schema::class, 'step_id');
    }

    public function data()
    {
        return $this->hasMany(SchemaDataStore::class, 'data_owner_id', 'id'); // Data_Owner_ID --> Step_ID
    }

    public function model()
    {
        return $this->belongsTo(ModelType::class, 'model_id');
    }

    public function fields() : HasMany
    {
        return $this->hasMany(DynamicModelField::class, 'step_id');
    }
}
