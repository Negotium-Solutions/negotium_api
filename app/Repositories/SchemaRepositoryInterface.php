<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

interface SchemaRepositoryInterface
{
    public function get(Request $request, int $id = null) : Array;

    public function create(Request $request) : Array;

    public function update(Request $request, int $id) : Array;

    public function delete(int $id) : Array;
}
