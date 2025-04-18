<?php

namespace App\Models\Tenant;

use App\definitions\ModelTypeDefinitions;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class Process extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'name'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'schema_table'
    ];

    public function getSchemaTableAttribute(): string
    {
        return $this->schema->name;
    }

    public function category() : BelongsTo
    {
        return $this->belongsTo(ProcessCategory::class, 'process_category_id');
    }

    public function steps() : HasMany
    {
        return $this->hasMany(Step::class, 'parent_id');
    }

    public function profiles() : HasManyThrough {
        return $this->hasManyThrough(
            Profile::class,
            ProfileProcess::class,
            'process_id',
            'id',
            'id',
            'profile_id'
        );
    }

    public function log() : HasOne {
        return $this->hasOne(ProcessLog::class)
            ->join('profiles', 'process_logs.profile_id', '=', 'profiles.id')
            ->select('process_logs.*');
    }

    public function dynamicModel($profile_process_id)
    {
        Session::put('table_name', $this->schema->name);
        $dynamicModel = new DynamicModel();

        if (empty($dynamicModel->where('parent_id', $profile_process_id)->first()->parent_id)) {
            $dynamicModel->parent_id = $profile_process_id;
            $dynamicModel->save();
        }

        return $dynamicModel->where('parent_id', $profile_process_id)->first();
    }

    public function schema() : BelongsTo {
        return $this->belongsTo(Schema::class);
    }
}
