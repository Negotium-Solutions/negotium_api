<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Requests\Tenant\DynamicModelRequest;
use App\Models\Tenant\DynamicModel;
use App\Models\Tenant\DynamicModelCategory;
use App\Models\Tenant\Schema;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Ramsey\Uuid\Type\Integer;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

class DynamicModelController extends BaseApiController
{
    /**
     * Get profile(s)
     *
     * @OA\Get(
     *       path="/{tenant}/dynamic-model/{id}",
     *       summary="Get a Dynamic Models",
     *       operationId="getDynamicModel",
     *       tags={"DynamicModel"},
     *       security = {{"BearerAuth": {}}},
     *       description="Authenticate using a bearer token",
     *       @OA\Parameter(name="id", description="Dynamic Model Id", required=false, in="path", @OA\Schema( type="string" )),
     *       @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=500,description="Internal server error")
     *  ),
     *
     * @OA\Get(
     *       path="/{tenant}/dynamic-model",
     *       summary="Get dynamic models",
     *       operationId="getDynamicModels",
     *       tags={"DynamicModel"},
     *       security = {{"BearerAuth": {}}},
     *       description="Authenticate using a bearer token",
     *       @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=500,description="Internal server error")
     *  )
     *
     * @param Request $request
     * @param Request $id
     * @return Response
     * @throws Exception
     */
    public function get(Request $request, $id = null) : Response
    {
        try {
            $request->merge(['table_name' => Schema::find($request->input('schema_id'))->table_name]);

            $query = Schema::with(['models']);

            if ( !empty($id) ) {
                $query = Schema::with(['models' => function ($_query) use ($id) {
                    $_query->where('id', $id);
                }]);
            }

            $query = $query->where('id', $request->input('schema_id'));

            if ($request->has('with') && ($request->input('with') != '')) {
                $_with = explode(',', $request->input('with'));
                $query = $query->with($_with)->first();

                if(!isset($query->models[0]->id)){
                    return $this->success([], 'No record(s) found for dynamic model', [], Response::HTTP_NOT_FOUND);
                }

                if (in_array('groups.fields', $_with)) {
                    foreach ($query->groups as $group_key => $group) {
                        foreach ($group->fields as $key => $field) {
                            $field['value'] = $query->models[0]->{$field->field};
                            $query->groups[$group_key]->fields[$key] = $field;
                        }
                    }
                }
            } else {
                $query = $query->first();
            }

            return $this->success($query, 'dynamic models successfully retrieved', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve dynamic model(s).', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function create(DynamicModelRequest $request) : Response
    {
        try {
            $dynamicModel = new DynamicModel();

            foreach ($request->input('groups') as $group) {
                foreach ($group['fields'] as $field) {
                    $dynamicModel->{$field['field']} = isset($field['value']) ? $field['value'] : null;
                }
            }
            $dynamicModel->schema_id = $request->input('id');
            $dynamicModel->updated_at = now();

            if ($dynamicModel->save() === false) {
                throw new \RuntimeException('Could not update dynamic model');
            }

            $new_value = $request->all();
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the dynamic model.', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success(['id' => $dynamicModel->id], 'Dynamic model created successfully.', $request->all(), Response::HTTP_CREATED, [], $new_value);
    }

    /**
     * Get new empty dynamic model record
     *
     * @OA\Get(
     *       path="/{tenant}/dynamic-model/new-record",
     *       summary="Get new empty Dynamic Model Record",
     *       operationId="getNewEmptyDynamicModelRecord",
     *       tags={"NewEmptyDynamicModelRecord"},
     *       security = {{"BearerAuth": {}}},
     *       description="Authenticate using a bearer token",
     *       @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=500,description="Internal server error")
     *  ),
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function newRecord(Request $request, $schema_id) : Response
    {
        try {
            $request->merge(['table_name' => Schema::find($schema_id)->table_name]);

            $query = Schema::where('id', $schema_id);

            if ($request->has('with') && ($request->input('with') != '')) {
                $_with = explode(',', $request->input('with'));
                $query = $query->with($_with)->first();

                if (in_array('groups.fields.validations', $_with)) {
                    foreach ($query->groups as $group_key => $group) {
                        foreach ($group->fields as $key => $field) {
                            $field['value'] = null;
                            $query->groups[$group_key]->fields[$key] = $field;
                        }
                    }
                }
            } else {
                $query = $query->first();
            }

            return $this->success($query, 'new dynamic model successfully retrieved.', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve new dynamic model.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get new empty dynamic model record
     *
     * @OA\Get(
     *       path="/{tenant}/dynamic-model/schema",
     *       summary="Get schema by dynamic model type id",
     *       operationId="getSchema",
     *       tags={"GetSchema"},
     *       security = {{"BearerAuth": {}}},
     *       description="Authenticate using a bearer token",
     *       @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=500,description="Internal server error")
     *  ),
     *
     * @param Request $request
     * @param Integer $type_id
     * @return Response
     * @throws Exception
     */
    public function getSchema(Request $request, $type_id) : Response
    {
        try {
            $query = Schema::where('dynamic_model_type_id', $type_id);

            if ($request->has('with') && ($request->input('with') != '')) {
                $_with = explode(',', $request->input('with'));
                $query = $query->with($_with);
            }

            $query = $query->get();

            if ($query->count() === 0) {
                return $this->success($query, "schema with type id {$type_id} not found.", [], Response::HTTP_NOT_FOUND);
            }

            return $this->success($query, 'schema successfully retrieved.', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve schema.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
