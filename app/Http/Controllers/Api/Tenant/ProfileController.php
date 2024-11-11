<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Requests\Tenant\DynamicModelFieldRequest;
use App\Http\Requests\Tenant\ProfileRequest;
use App\Models\Tenant\DynamicModel;
use App\Models\Tenant\DynamicModelFieldGroup;
use App\Models\Tenant\ProcessStatus;
use App\Models\Tenant\ProfileProcess;
use App\Models\Tenant\Schema as TenantSchema;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;
use Throwable;

class ProfileController extends BaseAPIController
{
    /**
     * Get profile(s)
     *
     * @OA\Get(
     *       path="/{tenant}/profile/{id}",
     *       summary="Get a Profile",
     *       operationId="getProfile",
     *       tags={"Profile"},
     *       security = {{"BearerAuth": {}}},
     *       description="Authenticate using a bearer token",
     *       @OA\Parameter(name="id", description="Profile Id", required=false, in="path", @OA\Schema( type="string" )),
     *       @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=500,description="Internal server error")
     *  ),
     *
     * @OA\Get(
     *       path="/{tenant}/profile",
     *       summary="Get profiles",
     *       operationId="getProfiles",
     *       tags={"Profile"},
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
     * @throws Throwable
     */
    public function get(Request $request, $id = null) : Response
    {
        try {
            $schema = new TenantSchema();
            $data = $schema->getDynamicModelsBySchema($request, $id);

            if(!isset($data->models[0]->id)){
                return $this->success([], 'No record(s) found for dynamic model', [], Response::HTTP_NOT_FOUND);
            }

            return $this->success($data, 'profiles successfully retrieved', [], Response::HTTP_OK);
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve tenant.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get new empty dynamic model record
     *
     * @OA\Get(
     *       path="/{tenant}/profile/create",
     *       summary="Get new empty profile",
     *       operationId="getNewEmptyProfile",
     *       tags={"NewEmptyProfile"},
     *       security = {{"BearerAuth": {}}},
     *       description="Authenticate using a bearer token",
     *       @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=500,description="Internal server error")
     *  ),
     *
     * @param Request $request
     * @return Response
     * @throws Throwable
     */
    public function form(Request $request, $schema_id) : Response
    {
        try {
            $query = TenantSchema::where('id', $schema_id);

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

            return $this->success($query, 'new profile successfully retrieved.', [], Response::HTTP_OK);
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve new profile.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new profile.
     *
     * @OA\Post(
     *        path="/{tenant}/profile/create",
     *        summary="Create a new profile",
     *        operationId="createProfile",
     *        tags={"Profile"},
     *        security = {{"BearerAuth": {}}},
     *        description="Authenticate using a bearer token",
     *        @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *        @OA\Response(response=422,description="Input validation error"),
     *        @OA\Response(response=500,description="Internal server error")
     * )
     *
     * @param ProfileRequest $request
     * @return Response
     * @throws Throwable
     */
    public function create(ProfileRequest $request) : Response
    {
        try {
            $dynamicModel = new DynamicModel();

            foreach ($request->input('groups') as $group) {
                foreach ($group['fields'] as $field) {
                    $dynamicModel->{$field['field']} = isset($field['value']) ? $field['value'] : null;
                }
            }
            switch($request->input('dynamic_model_category_id')) {
                case 1:
                    $dynamicModel->avatar = '/images/individual/avatar'.rand(1, 5).'.png';
                    break;
                case 2:
                    $dynamicModel->avatar = '/images/business/avatar'.rand(1, 5).'.png';;
                    break;
            }
            $dynamicModel->parent_id = $request->input('dynamic_model_category_id');
            $dynamicModel->schema_id = $request->input('id');
            $dynamicModel->updated_at = now();

            if ($dynamicModel->save() === false) {
                throw new \RuntimeException('Could not update dynamic model');
            }

            $new_value = $request->all();
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to create profile.', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success(['id' => $dynamicModel->id], 'Profile created successfully.', $request->all(), Response::HTTP_CREATED, [], $new_value);
    }

    public function edit(Request $request, $id) : Response
    {
        try {
            $dynamicModel = DynamicModel::find($id);
            $data = $dynamicModel->getRecord($request, $id);

            // return $this->success(['id' => $id], 'profile successfully retrieved', [], Response::HTTP_OK);
            return $this->success($data, 'profile successfully retrieved', [], Response::HTTP_OK);
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve profile.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a profile BY ID.
     *
     * @OA\Put(
     *        path="/{tenant}/profile/update/{id}",
     *        summary="Update a Profile",
     *        operationId="updateProfile",
     *        tags={"Profile"},
     *        security = {{"BearerAuth": {}}},
     *        description="Authenticate using a bearer token",
     *        @OA\Parameter(name="id", description="Profile Id", required=true, in="path", @OA\Schema( type="string" )),
     *        @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *        @OA\Response(response=422,description="Input validation error"),
     *        @OA\Response(response=404,description="Not found")
     *   ),
     *
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function update(DynamicModelFieldRequest $request, $id) : Response
    {
        try {
            $old_value = [];
            $new_value = [];
            // Todo: Update profile code
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the profile', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success(['id' => 0], 'profile successfully updated', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }

    /**
     * Delete a Profile by ID.
     *
     * @OA\Delete(
     *      path="/{tenant}/profile/delete/{id}",
     *      operationId="deleteProfileById",
     *      tags={"Profile"},
     *      security = {{"BearerAuth": {}}},
     *      description="Authenticate using a bearer token",
     *      @OA\Parameter(name="id", in="path", @OA\Schema(type="string")),
     *      @OA\Response(response=204, description="No content"),
     *      @OA\Response(response=404, description="Not found")
     * )
     *
     * @param String $id
     * @return Response
     * @throws Throwable
     */
    public function delete($id) : Response
    {
        try {
            $profile = DynamicModel::find($id);
            if((!isset($profile))){
                return $this->success([], 'No profile record found to delete', [], Response::HTTP_NOT_FOUND);
            }

            if ($profile->delete() === false) {
                throw new \RuntimeException('Could not delete the profile');
            }

            return response()->noContent();
        } catch (Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the profile', ['profile_id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Assign processes to a profile.
     *
     * @OA\AssignProcess(
     *      path="/{tenant}/profile/assign-process",
     *      operationId="assignProcess",
     *      tags={"Profile"},
     *      security = {{"BearerAuth": {}}},
     *      description="Authenticate using a bearer token",
     *      @OA\Parameter(name="profile_id", in="path", @OA\Schema(type="string")),
     *      @OA\Parameter(name="process_id", in="path", @OA\Schema(type="string")),
     *      @OA\Response(response=204, description="No content"),
     *      @OA\Response(response=404, description="Not found")
     * )
     *
     * @return Response
     * @throws Throwable
     */
    public function assignProcesses(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'data' => 'array|required'
        ]);

            if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            foreach ($request->input('data') as $data) {
                $profileProcess = ProfileProcess::where('profile_id', $data["profile_id"])->where('process_id', $data["process_id"])->first();
                if (!isset($profileProcess->id) || !($profileProcess->id > 0)) {
                    $step = DynamicModelFieldGroup::where('schema_id', $data["process_id"])
                        ->orderBy('order', 'asc')
                        ->first();

                    $profileProcess = new ProfileProcess();
                    $profileProcess->profile_id = $data["profile_id"];
                    $profileProcess->process_id = $data["process_id"];
                    $profileProcess->step_id = $step->id;
                    $profileProcess->started_by_user_id = auth()->user()->id;
                    $profileProcess->process_status_id = ProcessStatus::ASSIGNED;

                    if ($profileProcess->save() === false) {
                        throw new \RuntimeException('Could not assign process to profile');
                    }

                    $tenantSchema = TenantSchema::find($data["process_id"]);
                    Session::put('table_name', $tenantSchema->table_name);
                    $dynamicModel = new DynamicModel();
                    $dynamicModel->schema_id = $tenantSchema->id;
                    $dynamicModel->parent_id = $profileProcess->id;
                    $dynamicModel->save();
                }
            }

            return $this->success(['id' => $profileProcess->id], 'process successfully assigned to profile.', $request->all(), Response::HTTP_CREATED);
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to assign process to profile.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteProcess(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'profile_id' => 'string|required',
            'process_id' => 'string|required'
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $profileProcess = ProfileProcess::where('profile_id', $request->profile_id)->where('process_id', $request->process_id)->first();

            if (isset($profileProcess->id) && ($profileProcess->id > 0)) {
                $profileProcess->delete();
            }

            return response()->noContent();
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to delete process from profile.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getProfileProcesses(Request $request, $profile_id)
    {
        try {
            $profileProcesses = ProfileProcess::with(['process', 'step', 'status'])->where('profile_id', $profile_id)->get();

            return $this->success($profileProcesses, 'processes successfully retrieved.', $request->all(), Response::HTTP_CREATED, [], []);
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve processes.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
