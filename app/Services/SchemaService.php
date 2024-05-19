<?php

namespace App\Services;

use App\Models\Tenant\Activity;
use App\Models\Tenant\Step;
use App\Repositories\SchemaRepositoryInterface;

use Illuminate\Http\Request;
class SchemaService
{
    public function __construct(protected SchemaRepositoryInterface $schemaRepositoryInterface)
    {
    }

    public function get(Request $request, int $id = null) : Array
    {
        return $this->schemaRepositoryInterface->get($request, $id);
    }

    public function create(Step $step) : Array
    {
        return $this->schemaRepositoryInterface->create($step);
    }

    public function addColumn(Activity $activity) : Array
    {
        return $this->schemaRepositoryInterface->addColumn($activity);
    }

    public function updateColumn(Activity $activity) : Array
    {
        return $this->schemaRepositoryInterface->updateColumn($activity);
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
