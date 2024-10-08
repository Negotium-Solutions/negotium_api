<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Schema extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $hidden = [
        'deleted_at'
    ];

    protected $appends = [
        'has_data'
    ];

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

    public function createDynamicModel($name, $dynamic_model_category_id, $dynamic_model_type_id, $quick_capture)
    {
        $modelType = DynamicModelType::find($dynamic_model_type_id);
        $this->save();
        $this->name = $name;
        $this->table_name = strtolower(str_replace(' ', '_', trim($modelType->name).'_'.$this->id));
        $this->dynamic_model_category_id = $dynamic_model_category_id;
        $this->dynamic_model_type_id = $dynamic_model_type_id;
        $this->quick_capture = $quick_capture;
        $this->save();

        \Illuminate\Support\Facades\Schema::create($this->table_name, function (Blueprint $table) use ($dynamic_model_type_id) {
            $table->uuid('id')->primary();
            $table->uuid('schema_id')->nullable();
            if ($dynamic_model_type_id === DynamicModelType::PROCESS) {
                $table->uuid('profile_id')->nullable();
            }
            $table->timestamps();
            $table->softDeletes();
        });
    }

    // New Code
    public function createColumn($name, $type)
    {
        \Illuminate\Support\Facades\Schema::table($this->table_name, function (Blueprint $table) use ($name, $type) {
            $table->{$type}($name)->nullable();
        });
    }

    public function getHasDataAttribute()
    {
        return $this->table_name;
        // return $this->table_name::query()->qet()->count() > 0 ? true : false;
    }

    public function dynamicModel() : HasOne
    {
        return $this->hasOne(DynamicModel::class, 'parent_id');
    }

    public function steps() : HasMany
    {
        return $this->hasMany(Step::class, 'parent_id');
    }
}
