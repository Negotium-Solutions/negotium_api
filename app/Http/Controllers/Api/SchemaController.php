<?php

namespace App\Http\Controllers\Api;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;
use App\Models\Schema as CRMSchema;

class SchemaController extends BaseApiController
{
    /**
     * Get process(s) resource(s).
     */
    public function get(Request $request, $id = null) : Response
    {
        $query = isset($id) ? CRMSchema::find($id) : CRMSchema::query();

        $data = isset($id) ? $query : $query->get();

        return $this->success($data, 'schemas successfully retrieved', [], Response::HTTP_OK);
    }

    /**
     * Store a newly created schema
     */
    public function create(Request $request) : Response
    {
        $request_data = json_decode($request->getContent(), true);

        $validator = \Validator::make($request_data,
            ['name' => 'string|required'],
            ['columns' => 'required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), 422);
        }

        $table = $this->toCleanString($request_data['name']);
        $columns = $request_data['columns'];

        try {
            $schema = new CRMSchema();
            $schema->name = $table;
            if($request->has('model')) {
                $schema->model = $request->input('model');
            }
            if($request->has('parent_id')) {
                $schema->parent_id = $request->input('parent_id');
            }
            $schema->save();

            $schema->name = $schema->name.'_'.$schema->id;
            $schema->save();

            Schema::create($schema->name, function (Blueprint $table) use ($columns) {
                $table->bigIncrements('id');
                $colum_names = array_keys($columns);
                foreach ($colum_names as $column_name) {
                    $table->{$columns[$column_name]}($this->toCleanString($column_name))->nullable();
                }
                $table->timestamps();
                $table->softDeletes();
            });

            return $this->success(['table' => $table], 'Schema successfully created.', $request->all(), 200);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create schema.', []);
        }
    }

    /**
     * Update a schema
     */
    public function update(Request $request, $id) : Response
    {
        $request_data = json_decode($request->getContent(), true);

        $validator = \Validator::make($request_data,
            ['columns' => 'required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), 422);
        }

        $columns = $request_data['columns'];

        try {
            $schema = CRMSchema::find($id);

            Schema::table($schema->name, function (Blueprint $table) use ($columns) {
                $colum_names = array_keys($columns);
                foreach ($colum_names as $column_name) {
                    $table->{$columns[$column_name]}($this->toCleanString($column_name))->nullable();
                }
            });

            return $this->success(['table' => $schema->name], 'Schema successfully updated.', $request->all(), 200);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to update schema.', []);
        }
    }

    /**
     * Delete the schema
     */
    public function delete($id) : Response
    {
        try {
            $schema = CRMSchema::find($id);

            if ($schema->delete() === false) {
                throw new \RuntimeException('Could not delete the schema');
            }

            return $this->success([], 'schema successfully deleted', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the the schema', ['id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get columns.
     */
    public function columns($id) : Response
    {
        $schema = CRMSchema::find($id);

        $data = $schema->columns();

        return $this->success($data, 'schemas successfully retrieved', [], Response::HTTP_OK);
    }

    public function toCleanString($fieldName): string
    {
        $fieldName = trim($fieldName);
        $fieldName = str_replace(' ', 'abcba', $fieldName); // placeholder
        $fieldName = strtolower(str_replace(' ', '', preg_replace('/[\W]/', '', $fieldName)));
        $fieldName = str_replace('abcba', '_', $fieldName);
        $fieldName = str_replace('__', '_', $fieldName);

        return $fieldName;
    }
}
