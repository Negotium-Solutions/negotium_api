<?php

namespace App\Repositories;

use App\Models\Tenant\Activity;
use App\Models\Tenant\Schema as CRMSchema;
use App\Models\Tenant\Step;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SchemaRepository implements SchemaRepositoryInterface
{
    public function get(Request $request, int $id = null): Array
    {
        $query = isset($id) ? CRMSchema::find($id) : CRMSchema::query();

        $data = isset($id) ? $query : $query->get();

        return  $data->toArray();
    }

    public function create(Step $step) : Array
    {
        try {
            $tableName = $this->toCleanString($step->name);

            $schema = new CRMSchema();
            $schema->name = $tableName;
            $schema->step_id = $step->id;
            $schema->save();

            $schema->name = $schema->name.'_'.$schema->id;
            $schema->save();

            Schema::create($schema->name, function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('owner_id')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            return ['status' => 'success', 'message' => 'Schema successfully created.'];
        } catch (\Throwable $exception) {
            return ['status' => 'error', 'message' => $exception->getMessage()];
        }
    }

    public function addColumn(Activity $activity) : Array
    {
        try {
            Schema::table($activity->step->schema->name, function (Blueprint $table) use ($activity) {
                $table->{$activity->type->schema_data_type}($this->toCleanString($activity->name))->nullable()->comment(json_encode(['name' => $activity->name, 'label' => $activity->label, 'type_id' => $activity->type_id, 'attributes' => $activity->attributes]));
            });

            return ['status' => 'success', 'message' => 'Schema column successfully added'];
        } catch (\Throwable $exception) {
            return ['status' => 'error', 'message' => $exception->getMessage()];
        }
    }

    public function updateColumn(Activity $activity) : Array
    {
        try {
            Schema::table($activity->step->schema->name, function (Blueprint $table) use ($activity) {
                $table->{$activity->type->schema_data_type}($this->toCleanString($activity->name))->nullable()->comment(json_encode(['name' => $activity->name, 'label' => $activity->label, 'type_id' => $activity->type_id, 'attributes' => $activity->attributes]))->change();
            });

            return ['status' => 'success', 'message' => 'Schema column successfully added'];
        } catch (\Throwable $exception) {
            return ['status' => 'error', 'message' => $exception->getMessage()];
        }
    }

    /**
     * Update a schema with multiple columns
     */
    public function update(Request $request, int $id) : Array
    {
        $request_data = json_decode($request->getContent(), true);

        $columns = $request_data['columns'];

        try {
            $schema = CRMSchema::find($id);
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

            return ['message' => 'Schema successfully updated.', 'request' => $request->all()];
        } catch (\Throwable $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    /**
     * Delete the schema
     */
    public function delete(int $id) : Array
    {
        try {
            $schema = CRMSchema::find($id);
            if((!isset($schema))){
                return ['status' => 'error', 'message' => 'No schema record found to delete'];
            }

            if ($schema->delete() === false) {
                throw new \RuntimeException('Could not delete the schema');
                return ['status' => 'error', 'message' => 'Could not delete the schema'];
            }

            return ['status' => 'success', 'message' => 'Item deleted successfully'];
        } catch (\Throwable $exception) {
            return ['status' => 'error', 'message' => 'There was an error trying to delete the the schema'];
        }
    }

    /**
     * Store a newly created schema with multiple columns
     */
    public function createMultipleColumns(Request $request) : Array
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
                $table->integer('schema_id')->nullable();
                $table->integer('data_owner_id')->nullable();
                foreach ($columns as $column) {
                    $table->{$column['type']}($this->toCleanString($column['name']))->nullable()->comment(json_encode($column));
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
     * Get columns.
     */
    public function getColumns(int $id) : Array
    {
        try{
            $schema = CRMSchema::find($id);

            $data = $schema->getColumns();

            if((!isset($data)) || (!isset($data['data']))){
                return ['message' => 'No tenant record(s) found'];
            }

            return ['message' => 'schemas successfully retrieved'];
        } catch (\Throwable $exception) {
            return ['message' => 'An error occurred while trying to retrieve tenant.'];
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
