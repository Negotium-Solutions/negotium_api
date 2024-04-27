<?php

namespace App\Services;

use App\Repositories\SchemaRepositoryInterface;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
class SchemaService
{
    public function __construct(protected SchemaRepositoryInterface $schemaRepositoryInterface)
    {
    }

    public function get(Request $request, int $id = null) : Array
    {
        return $this->schemaRepositoryInterface->get($request, $id);
    }

    public function create(Request $request) : Array
    {
        return $this->schemaRepositoryInterface->create($request);
    }

    public function update(Request $request, int $id) : Array
    {
        return $this->schemaRepositoryInterface->update($request, $id);
    }

    public function delete(int $id) : Array
    {
        return $this->schemaRepositoryInterface->delete($id);
    }
}
