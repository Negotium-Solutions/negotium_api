<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
