<?php

namespace App\Http\Controllers\Api;

use App\Models\SchemaDataStore;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;
use App\Models\Schema as CRMSchema;

class SchemaDataStoreController extends BaseApiController
{
    /**
     * Get item resource(s).
     */
    public function get(Request $request, $id = null) : Response
    {
        $request_data = json_decode($request->getContent(), true);

        $validator = \Validator::make($request_data,
            ['schema_id' => 'integer|required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), 422);
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

        return $this->success($data, 'Items successfully retrieved', [], Response::HTTP_OK);
    }

    /**
     * Create an item
     */
    public function create(Request $request) : Response
    {
        $request_data = json_decode($request->getContent(), true);

        $validator = \Validator::make($request_data,
            ['schema_id' => 'integer|required'],
            ['data' => 'required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), 422);
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

            return $this->success(['table' => $schema->name], 'Schema data successfully stored.', $request->all(), 200);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to store schema data.', []);
        }
    }

    /**
     * Update the item
     */
    public function update(Request $request, $id) : Response
    {
        $request_data = json_decode($request->getContent(), true);

        $validator = \Validator::make($request_data,
            ['schema_id' => 'integer|required'],
            ['data' => 'required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), 422);
        }

        try {
            $schema = CRMSchema::find($request_data['schema_id']);

            $schemaDataStore = new SchemaDataStore;
            $schemaDataStore->getTable();
            $schemaDataStore->setTable($schema->name);
            $schemaDataStore = $schemaDataStore->where('id',$id)->first();

            foreach ($request_data['data'] as $column)
            {
                $schemaDataStore->{$column['name']} = $column['value'];
            }

            if ($schemaDataStore->save() === false) {
                throw new \RuntimeException('Could not save process category');
            }

            return $this->success(['table' => $schema->name], 'Schema data successfully stored.', $request->all(), 200);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to store schema data.', []);
        }
    }

    /**
     * Delete the item
     */
    public function delete(Request $request, $id) : Response
    {
        $request_data = json_decode($request->getContent(), true);

        $validator = \Validator::make($request_data,
            ['schema_id' => 'integer|required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), 422);
        }

        try {
            $schema = CRMSchema::find($request_data['schema_id']);

            $schemaDataStore = new SchemaDataStore;
            $schemaDataStore->getTable();
            $schemaDataStore->setTable($schema->name);
            $schemaDataStore = $schemaDataStore->where('id',$id)->first();

            if ($schemaDataStore->delete() === false) {
                throw new \RuntimeException('Could not delete the item');
            }

            return $this->success([], 'Item successfully deleted', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the the item', ['id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
