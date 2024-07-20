<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    public function processes() {
        return $this->hasManyThrough(
            Process::class,
            ProfileProcess::class,
            'profile_id',
            'id',
            'id',
            'process_id'
        );
    }

    public function documents() {
        return $this->hasMany(Document::class);
    }
}
