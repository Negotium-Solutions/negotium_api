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
