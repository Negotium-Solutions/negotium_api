<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Tenant\Schema as TenantSchema;

class ProfileProcess extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $hidden = [
        'deleted_at'
    ];

    protected $appends = ['processData'];

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

    public function getProcessDataAttribute()
    {
        $tenantSchema = TenantSchema::find($this->process_id);
        Session::put('table_name', $tenantSchema->table_name);
        $processData = DB::table($tenantSchema->table_name)
               ->where('parent_id', $this->id)
               ->first();

        return $processData;
    }
}
