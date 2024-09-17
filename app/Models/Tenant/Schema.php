<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class Schema extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

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

    public function setName($name) : void
    {
        $this->save();
        $this->name = $name.'_'.$this->id;
        $this->save();
    }

    public function createSchema($name)
    {
        $this->save();
        $this->name = $name.'_'.$this->id;
        $this->save();

        \Illuminate\Support\Facades\Schema::create($this->name, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function createField($_table, $name, $type)
    {
        \Illuminate\Support\Facades\Schema::table($_table, function (Blueprint $table) use ($name, $type) {
            $table->{$type}($name)->nullable();
        });
    }
}
