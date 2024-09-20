<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Requests\Tenant\DynamicModelFieldSingularRequest;
use App\Models\Tenant\DynamicModelField;
use App\Models\Tenant\DynamicModelFieldType;
use App\Models\Tenant\Schema as TenantSchema;
use App\Models\Tenant\Step;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

class DynamicModelFieldController extends BaseApiController
{
    /**
     * Get step(s)
     *
     * @OA\Get(
     *       path="/{tenant}/dynamic-model-field/{id}",
     *       summary="Get a DynamicModelField",
     *       operationId="getDynamicModelField",
     *       tags={"DynamicModelField"},
     *       security = {{"BearerAuth": {}}},
     *       description="Authenticate using a bearer token",
     *       @OA\Parameter(name="id", description="DynamicModelField Id", required=false, in="path", @OA\Schema( type="string" )),
     *       @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=500,description="Internal server error")
     *  ),
     *
     * @OA\Get(
     *       path="/{tenant}/dynamic-model-field",
     *       summary="Get DynamicModelFields",
     *       operationId="getDynamicModelFields",
     *       tags={"DynamicModelField"},
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
    public function get(Request $request, $step_id = null) : Response
    {
        try{
            $query = isset($step_id) ? DynamicModelField::where('step_id', $step_id) : DynamicModelField::query();

            if ($request->has('with')) {
                $with_array = explode(',', $request->with);
                $query = $query->with($with_array);
            }

            $data = isset($id) ? $query->first() : $query->get();

            if ((isset($id) && !isset($data)) || (!isset($id) && count($data) == 0)) {
                return $this->success([], 'No dynamic model field record(s) found', [], Response::HTTP_NOT_FOUND);
            }

            return $this->success($data, 'dynamic model field(s) successfully retrieved', [], Response::HTTP_OK);
        }catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve.', [], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Create a new dynamic field model.
     *
     * @OA\Post(
     *        path="/{tenant}/dynamic-model-/create",
     *        summary="Create a new dynamic-model-field",
     *        operationId="createDynamicFieldModel",
     *        tags={"DynamicFieldModel"},
     *        security = {{"BearerAuth": {}}},
     *        description="Authenticate using a bearer token",
     *        @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *        @OA\Response(response=422,description="Input validation error"),
     *        @OA\Response(response=500,description="Internal server error")
     * )
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function create(DynamicModelFieldSingularRequest $request) : Response
    {
        try {
            $dynamicModelField = new DynamicModelField();
            $dynamicModelField->label = $request->get('name');
            $dynamicModelField->step_id = $request->get('step_id');
            $dynamicModelField->dynamic_model_field_type_id = $request->get('dynamic_model_field_type_id');
            $dynamicModelField->setField($dynamicModelField->label);
            $dynamicModelField->order = $dynamicModelField->id;

            $step = Step::find($dynamicModelField->step_id);
            $schema_id = $step->process->schema_id;
            $tenantSchema = TenantSchema::find($schema_id);

            $dynamicModelFieldType = DynamicModelFieldType::find($dynamicModelField->dynamic_model_field_type_id);
            $tenantSchema->createField($tenantSchema->name, $dynamicModelField->field, $dynamicModelFieldType->data_type);

            if ($dynamicModelField->save() === false) {
                throw new \RuntimeException('Could not save note');
            }

            return $this->success(['id' => $dynamicModelField->id], 'Dynamic model field successfully created.', $request->all(), Response::HTTP_CREATED);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create dynamic model field.', [],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
