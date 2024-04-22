<?php

namespace App\Repositories;

use App\Models\Tenant\Schema as CRMSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class SchemaRepository implements SchemaRepositoryInterface
{
    public function get(Request $request, int $id = null): Array
    {
        $query = isset($id) ? CRMSchema::find($id) : CRMSchema::query();

        $data = isset($id) ? $query : $query->get();

        return  $data->toArray();
    }

    /**
     * Store a newly created schema
     */
    public function create(Request $request) : Array
    {
        $request_data = json_decode($request->getContent(), true);

        try {
            $table = $this->toCleanString($request_data['name']);
            $columns = $request_data['columns'];

            $schema = new CRMSchema();
            $schema->name = $table;
            if($request->has('model')) {
                $schema->model = $request->input('model');
            }
            if($request->has('parent_id')) {
                $schema->parent_id = $request->input('parent_id');
            }
            $schema->columns = json_encode($columns);
            $schema->save();

            $schema->name = $schema->name.'_'.$schema->id;
            $schema->save();

            Schema::create($schema->name, function (Blueprint $table) use ($columns) {
                $table->bigIncrements('id');
                foreach ($columns as $column) {
                    $table->{$column['type']}($this->toCleanString($column['name']))->nullable()->comment(json_encode($column['attributes']));
                }
                $table->timestamps();
                $table->softDeletes();
            });

            return [['table' => $table], 'message' => 'Schema successfully created.', 'request' => $request->all()];
        } catch (\Throwable $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    /**
     * Update a schema
     */
    public function update(Request $request, int $id) : Response
    {
        $request_data = json_decode($request->getContent(), true);

        $validator = Validator::make($request_data,
            ['columns' => 'required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $columns = $request_data['columns'];

        try {
            $schema = CRMSchema::find($id);
            if((!isset($tenant))){
                return $this->success([], 'No schema record found to update', [], Response::HTTP_NOT_FOUND);
            }

            $schema->columns = json_encode($columns);
            $schema->save();

            $existing_columns = [];
            foreach ($schema->getColumns() as $schema_column) {
                $existing_columns[] = $schema_column['name'];
            }

            Schema::table($schema->name, function (Blueprint $table) use ($schema, $columns, $existing_columns) {
                foreach ($columns as $column) {
                    if(isset($column['deleted_at']) && $column['deleted_at'] != '') {
                        $column['attributes']['deleted_at'] = $column['deleted_at'];
                    }
                    if (!in_array($column['name'], $existing_columns)) {
                        $table->{$column['type']}($this->toCleanString($column['name']))->nullable()->comment(json_encode($column['attributes']));
                    } else {
                        $table->{$column['type']}($this->toCleanString($column['name']))->nullable()->comment(json_encode($column['attributes']))->change();
                    }
                }
            });

            return $this->success(['table' => $schema->name], 'Schema successfully updated.', $request->all(), Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to update schema.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete the schema
     */
    public function delete(int $id) : Response
    {
        try {
            $schema = CRMSchema::find($id);
            if((!isset($schema))){
                return $this->success([], 'No schema record found to delete', [], Response::HTTP_NOT_FOUND);
            }

            if ($schema->delete() === false) {
                throw new \RuntimeException('Could not delete the schema');
            }

            return response()->noContent();
        } catch (\Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the the schema', ['id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get columns.
     */
    public function getColumns(int $id) : Response
    {
        try{
            $schema = CRMSchema::find($id);

            $data = $schema->getColumns();

            if((!isset($data)) || (!isset($data['data']))){
                return $this->success([], 'No tenant record(s) found', [], Response::HTTP_NOT_FOUND);
            }

            return $this->success($data, 'schemas successfully retrieved', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve tenant.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function toCleanString(string $fieldName): string
    {
        $fieldName = trim($fieldName);
        $fieldName = str_replace(' ', 'abcba', $fieldName); // placeholder
        $fieldName = strtolower(str_replace(' ', '', preg_replace('/[\W]/', '', $fieldName)));
        $fieldName = str_replace('abcba', '_', $fieldName);
        $fieldName = str_replace('__', '_', $fieldName);

        return $fieldName;
    }
}
