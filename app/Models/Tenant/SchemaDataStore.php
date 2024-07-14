<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchemaDataStore extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = ''; // Set this on when initialising the instance of this model

    public function getFullName() {
        switch (Step::class) {

        }
    }
}
