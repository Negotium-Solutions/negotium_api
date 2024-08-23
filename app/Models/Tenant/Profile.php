<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'company_name'
    ];

    public function profile_type()
    {
        return $this->belongsTo(ProfileType::class);
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
            ->orderBy('reminder_datetime', 'desc')
            ->orderBy('created_at', 'desc');
    }

    public function communications() : HasMany
    {
        return $this->hasMany(Note::class)
            ->orderBy('created_at', 'desc');
    }

    public function extraData($table) : HasOne
    {
        return $this->hasOne($table::class, 'parent_id');
    }

    public function storeExtraDate($table, $data) : bool {
        return true;
    }
}
