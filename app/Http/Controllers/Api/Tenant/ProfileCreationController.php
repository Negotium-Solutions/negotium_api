<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Requests\Tenant\SchemaRequest;
use App\Models\Tenant\Schema as TenantSchema;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

class ProfileCreationController extends BaseApiController
{
    public function create(SchemaRequest $request) : Response
    {
        try {
            $schema = new TenantSchema();
            $schema->createDynamicModel(
                $request->get('name'),
                $request->get('dynamic_model_category_id'),
                $request->get('dynamic_model_type_id'),
                $request->get('quick_capture')
            );

            return $this->success(['id' => $schema->id], 'Schema successfully created.', $request->all(), Response::HTTP_CREATED);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create schema.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
