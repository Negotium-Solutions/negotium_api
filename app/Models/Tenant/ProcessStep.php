<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'process_id'
    ];

    public function process()
    {
        return $this->belongsTo(Process::class, 'process_id');
    }
}
