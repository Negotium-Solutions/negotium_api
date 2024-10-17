<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfileProcess extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $hidden = [
        'deleted_at'
    ];

    public function process() : BelongsTo
    {
        return $this->BelongsTo(Schema::class, 'process_id');
    }

    public function step() : BelongsTo
    {
        return $this->BelongsTo(DynamicModelFieldGroup::class, 'step_id');
    }

    public function status() : BelongsTo
    {
        return $this->BelongsTo(ProcessStatus::class, 'process_status_id');
    }
}
