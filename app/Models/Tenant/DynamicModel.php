<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DynamicModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'business_1';

    public function setDynamicTable(string $tableName): void
    {
        $this->setTable($tableName);
    }
}
