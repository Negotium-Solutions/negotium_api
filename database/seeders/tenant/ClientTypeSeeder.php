<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\ClientType;
use App\Models\Tenant\Schema as CRMSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ClientTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $request = json_decode(file_get_contents('database/data/client_type_personal_lines.json'));
        $this->addClientType($request);
        $request = json_decode(file_get_contents('database/data/client_type_business_lines.json'));
        $this->addClientType($request);
    }

    public function addClientType(Object $request)
    {
        $clientType = new ClientType();
        $clientType->name = $request->name;
        $clientType->save();

        $table = $this->toCleanString($request->name);

        $schema = new CRMSchema();
        $schema->name = $table;
        $schema->model = $request->model;
        $schema->parent_id = $clientType->id;
        $schema->columns = json_encode($request->columns);
        $schema->save();

        $columns = $request->columns;

        Schema::create($schema->name, function (Blueprint $table) use ($columns) {
            $table->bigIncrements('id');
            $table->integer('schema_id')->nullable();
            $table->integer('data_owner_id')->nullable();
            foreach ($columns as $column) {
                $table->{$column->type}($this->toCleanString($column->name))->nullable()->comment(json_encode($column));
            }
            $table->timestamps();
            $table->softDeletes();
        });
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
