<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Schema extends Model
{
    use HasFactory, SoftDeletes;

    public function getColumns()
    {
        $raw_columns = DB::select("SELECT column_name as name, column_type as type, column_comment as attributes FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$this->name."'");

        $columns = [];
        foreach ($raw_columns as $key => $column) {
            $attributes = json_decode($column->attributes, true);
            if(!(isset($attributes['deleted_at']) && $attributes['deleted_at'] != '')) {
                $columns[$key] = [
                    'name' => $column->name,
                    'type' => $column->type,
                    'attributes' => $attributes
                ];
            }
        }

        return $columns;
    }

    public function data()
    {
        return $this->hasMany(SchemaDataStore::class, 'data_owner_id');
    }
}
