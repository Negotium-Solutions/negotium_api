<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Models\Tenant\Schema as CRMSchema;
use App\Models\Tenant\SchemaDataStore;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;
use Illuminate\Support\Facades\Validator;

class SchemaDataStoreController extends BaseApiController
{
    /**
     * Get item resource(s).
     */
    public function get(Request $request, $id = null) : Response
    {
        $request_data = json_decode($request->getContent(), true);

        $validator = Validator::make($request_data,
            ['schema_id' => 'integer|required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $schemaDataStore = new SchemaDataStore;
        try {
            $schema = CRMSchema::find($request_data['schema_id']);

            $schemaDataStore->getTable();
            $schemaDataStore->setTable($schema->name);
        } catch (\Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to retrieve items', ['id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $data = isset($id) ? $schemaDataStore->where('id', $id)->first() : $schemaDataStore->get();
        if((isset($id) && !isset($data)) || (!isset($id) && count($data) == 0)){
            return $this->success([], 'No Items record(s) found', [], Response::HTTP_NOT_FOUND);
        }

        return $this->success($data, 'Items successfully retrieved', [], Response::HTTP_OK);
    }

    /**
     * Create an item
     */
    public function create(Request $request) : Response
    {
        $request_data = json_decode($request->getContent(), true);

        $validator = Validator::make($request_data,
            ['schema_id' => 'integer|required'],
            ['data' => 'required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $schema = CRMSchema::find($request_data['schema_id']);

            $schemaDataStore = new SchemaDataStore();
            $schemaDataStore->setTable($schema->name);

            foreach ($request_data['data'] as $column)
            {
                $schemaDataStore->{$column['name']} = $column['value'];
            }

            if ($schemaDataStore->save() === false) {
                throw new \RuntimeException('Could not save process category');
            }

            return $this->success(['table' => $schema->name], 'Schema data successfully stored.', $request->all(), Response::HTTP_CREATED);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to store schema data.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the item
     */
    public function update(Request $request, $id) : Response
    {
        $request_data = json_decode($request->getContent(), true);

        $validator = Validator::make($request_data,
            ['schema_id' => 'integer|required'],
            ['data' => 'required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $schema = CRMSchema::find($request_data['schema_id']);
            if((!isset($schema))){
                return $this->success([], 'No schema record found to update', [], Response::HTTP_NOT_FOUND);
            }

            $schemaDataStore = new SchemaDataStore;
            $schemaDataStore->getTable();
            $schemaDataStore->setTable($schema->name);
            $schemaDataStore = $schemaDataStore->where('id',$id)->first();

            foreach ($request_data['data'] as $column)
            {
                $schemaDataStore->{$column['name']} = $column['value'];
            }

            if ($schemaDataStore->save() === false) {
                throw new \RuntimeException('Could not save schema data');
            }

            return $this->success(['table' => $schema->name], 'Schema data successfully stored.', $request->all(), Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to store schema data.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete the item
     */
    public function delete(Request $request, $id) : Response
    {
        $request_data = json_decode($request->getContent(), true);

        $validator = Validator::make($request_data,
            ['schema_id' => 'integer|required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $schema = CRMSchema::find($request_data['schema_id']);
            if((!isset($schema))){
                return $this->success([], 'No schema record found to delete', [], Response::HTTP_NOT_FOUND);
            }

            $schemaDataStore = new SchemaDataStore;
            $schemaDataStore->getTable();
            $schemaDataStore->setTable($schema->name);
            $schemaDataStore = $schemaDataStore->where('id',$id)->first();
            if((!isset($schemaDataStore))){
                return $this->success([], 'No schema data record found to delete', [], Response::HTTP_NOT_FOUND);
            }

            if ($schemaDataStore->delete() === false) {
                throw new \RuntimeException('Could not delete the item');
            }

            return $this->success([], 'Item successfully deleted', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the the item', ['id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
