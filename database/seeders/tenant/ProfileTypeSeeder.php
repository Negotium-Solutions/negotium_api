<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\Activity;
use App\Models\Tenant\ActivityType;
use App\Models\Tenant\ProfileType;
use App\Models\Tenant\Schema as CRMSchema;
use App\Models\Tenant\Step;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use SebastianBergmann\Type\VoidType;

class ProfileTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientType = new ProfileType();
        $clientType->name = 'Individual';
        $clientType->save();
        /*
        $request = json_decode(file_get_contents('database/data/client_type_step1_personal_lines.json'));
        $this->addClientType($request);
        $request = json_decode(file_get_contents('database/data/client_type_step2_personal_lines.json'));
        $this->addClientType($request);
        */

        $clientType = new ProfileType();
        $clientType->name = 'Business';
        $clientType->save();
        /*
        $request = json_decode(file_get_contents('database/data/client_type_step1_business_lines.json'));
        $this->addClientType($request);
        $request = json_decode(file_get_contents('database/data/client_type_step2_business_lines.json'));
        $this->addClientType($request);
        */
    }

    public function addClientType(Object $request) : void
    {
        $step = new Step();
        $step->id = $request->step->id;
        $step->name = $request->step->name;
        $step->parent_id = $request->step->parent_id;
        $step->order = $request->step->order;
        $step->model_id = $request->step->model_id;
        $step->save();

        $schema = new CRMSchema();
        $schema->name = $request->step->schema->name;
        $schema->step_id = $request->step->schema->step_id;
        $schema->save();

        $columns = $request->step->activities;

        foreach ($request->step->activities as $_activity) {
            $activity = new Activity();
            $activity->name = $_activity->name;
            $activity->label = $_activity->label;
            $activity->attributes = $_activity->attributes;
            $activity->type_id = $_activity->type_id;
            $activity->step_id = $_activity->step_id;
            $activity->save();
        }

        Schema::create($schema->name, function (Blueprint $table) use ($columns) {
            $table->bigIncrements('id');
            $table->integer('data_owner_id')->nullable();
            foreach ($columns as $column) {
                $activityType = ActivityType::find($column->type_id);
                $table->{$activityType->schema_data_type}($this->toCleanString($column->name))->nullable()->comment(json_encode($column));
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
