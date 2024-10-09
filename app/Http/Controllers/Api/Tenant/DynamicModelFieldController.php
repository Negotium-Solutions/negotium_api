<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Requests\Tenant\DynamicModelFieldRequest;
use App\Http\Requests\Tenant\DynamicModelFieldSingularRequest;
use App\Models\Tenant\DynamicModel;
use App\Models\Tenant\DynamicModelField;
use App\Models\Tenant\DynamicModelFieldOption;
use App\Models\Tenant\DynamicModelFieldType;
use App\Models\Tenant\Schema as TenantSchema;
use App\Models\Tenant\Step;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

class DynamicModelFieldController extends BaseApiController
{
    const RADIO = 7;
    const CHECKBOX = 8;
    const DROPDOWN = 9;
    const EMAIL = 13;

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
    public function get(Request $request, int $step_id = null, int $id = null) : Response
    {
        try{
            $query = isset($id) ? DynamicModelField::where('step_id', $step_id)->where('id', $id) : DynamicModelField::where('step_id', $step_id);
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

            if (in_array($dynamicModelField->dynamic_model_field_type_id, [self::RADIO, self::CHECKBOX, self::DROPDOWN])) {
                foreach ($request->get('options') as $option) {
                    $dynamicModelFieldTypeOption = new DynamicModelFieldOption();
                    $dynamicModelFieldTypeOption->name = $option;
                    $dynamicModelFieldTypeOption->dynamic_model_field_id = $dynamicModelField->id;
                    $dynamicModelFieldTypeOption->save();
                }
            }

            return $this->success(['id' => $dynamicModelField->id], 'Dynamic model field successfully created.', $request->all(), Response::HTTP_CREATED);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create dynamic model field.', [],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateFields(DynamicModelFieldRequest $request) : Response
    {
        try {
            Session::put('table_name', $request->input('schema_table'));
            $dynamicModel = new DynamicModel();
            $dynamicModel = $dynamicModel->where('parent_id', $request->input('parent_id'))->first();

            $old_value = $dynamicModel;
            $new_value = $request->all();

            if ((!isset($dynamicModel))) {
                return $this->success([], 'No dynamic model record found to update', [], Response::HTTP_NO_CONTENT);
            }

            /*
            $fields = null;
            if ($request->has('step_id') && $request->input('step_id') > 0) {
                $steps = $request->input('steps');
                foreach ($steps as $step) {
                    if ($step['id'] === $request->input('step_id')) {
                        $fields = $step['fields'];
                    }
                }
            }

            if ($fields === null) {
                $fields = $request->all();
            }
            */

            foreach ($request->all() as $key => $value) {
            // foreach ($fields as $key => $value) {
                if (array_key_exists($key, $dynamicModel->getAttributes())) {
                    if (!in_array($key, ['id', 'created_at', 'updated_at', 'deleted_at', 'parent_id'])) {
                        $dynamicModel->{$key} = $value;
                    }
                }
            }
            $dynamicModel->updated_at = now();

            if ($dynamicModel->save() === false) {
                throw new \RuntimeException('Could not update profile dynamic model');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the profile', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success(['id' => $dynamicModel->id], 'profile successfully updated', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }

    /**
     * Delete a dynamic-model-field by ID.
     *
     * @OA\Delete(
     *      path="/{tenant}/dynamic-model-field/delete/{id}",
     *      operationId="deletedynamic-model-fieldById",
     *      tags={"Activity"},
     *      security = {{"BearerAuth": {}}},
     *      description="Authenticate using a bearer token",
     *      @OA\Parameter(name="id", in="path", @OA\Schema(type="string")),
     *      @OA\Response(response=204, description="No content"),
     *      @OA\Response(response=404, description="Not found")
     * )
     *
     * @param String $id
     * @return Response
     * @throws Exception
     */
    public function delete(int $id) : Response
    {
        try {
            $activity = DynamicModelField::find($id);
            if((!isset($activity))){
                return $this->success([], 'No activity record found to delete', [], Response::HTTP_NOT_FOUND);
            }

            if ($activity->delete() === false) {
                throw new \RuntimeException('Could not delete the activity');
            }

            return response()->noContent();
        } catch (\Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the activity', ['activity_id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
