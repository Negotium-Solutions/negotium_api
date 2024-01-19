<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema as LaravelSchema;

class Schema extends Model
{
    use HasFactory, SoftDeletes;

    public function schema()
    {
        return $this->belongsTo(Schema::class, 'parent_id');
    }

    public function columns()
    {
        $colums = [];
        foreach (LaravelSchema::getColumnListing($this->name) as $column) {
            $columns[] = [$column, LaravelSchema::getColumnType($this->name, $column)];
        }

        return $columns;
    }
}
