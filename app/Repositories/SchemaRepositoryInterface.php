<?php

namespace App\Repositories;

use App\Models\Tenant\Activity;
use App\Models\Tenant\Step;
use Illuminate\Http\Request;

interface SchemaRepositoryInterface
{
    public function get(Request $request, int $id = null) : Array;

    public function create(Step $step) : Array;

    public function addColumn(Activity $activity) : Array;

    public function update(Request $request, int $id) : Array;

    public function delete(int $id) : Array;
}
