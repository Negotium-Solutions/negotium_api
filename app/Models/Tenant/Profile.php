<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    const PROFILE_TYPE_INDIVIDUAL = 1;
    const PROFILE_TYPE_BUSINESS = 2;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $appends = [
        'profile_name',
        'email',
        'cell_number'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function profile_type()
    {
        return $this->belongsTo(ProfileType::class);
    }

    public function getProfileNameAttribute()
    {
        return (int)($this->profile_type_id) === self::PROFILE_TYPE_INDIVIDUAL ? $this->dynamicModel()->first_name.' '.$this->dynamicModel()->last_name : $this->dynamicModel()->company_name;
    }

    public function getEmailAttribute()
    {
        return (int)($this->profile_type_id) === self::PROFILE_TYPE_INDIVIDUAL ? $this->dynamicModel()->email : $this->dynamicModel()->email;
    }

    public function getCellNumberAttribute()
    {
        return (int)($this->profile_type_id) === self::PROFILE_TYPE_INDIVIDUAL ? $this->dynamicModel()->cell_number : $this->dynamicModel()->cell_number;
    }

    public function processes() : HasManyThrough
    {
        return $this->hasManyThrough(
            Process::class,
            ProfileProcess::class,
            'profile_id',
            'id',
            'id',
            'process_id'
        );
    }

    public function documents() : HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function notes() : HasMany
    {
        return $this->hasMany(Note::class)
            ->orderBy('reminder_datetime', 'asc')
            ->orderBy('created_at', 'desc');
    }

    public function communications() : HasMany
    {
        return $this->hasMany(Communication::class)
            ->orderBy('created_at', 'desc');
    }

    public function schema() : BelongsTo
    {
        return $this->belongsTo(Schema::class);
    }

    public function dynamicModel()
    {
        $dynamicModel = new DynamicModel();
        $dynamicModel->setTable($this->schema->name);

        return $dynamicModel->where('parent_id', $this->id)->first();
    }
}
