<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Requests\Tenant\DynamicModelFieldRequest;
use App\Http\Requests\Tenant\DynamicModelRequest;
use App\Http\Requests\Tenant\ProfileRequest;
use App\Models\Tenant\DynamicModel;
use App\Models\Tenant\ProcessLog;
use App\Models\Tenant\ProcessStatus;
use App\Models\Tenant\Profile;
use App\Models\Tenant\ProfileProcess;
use App\Models\Tenant\Schema as TenantSchema;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

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
     * @throws Exception
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
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve tenant.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
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
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function create(DynamicModelRequest $request) : Response
    {
        try {
            $dynamicModel = new DynamicModel();

            foreach ($request->input('groups') as $group) {
                foreach ($group['fields'] as $field) {
                    $dynamicModel->{$field['field']} = isset($field['value']) ? $field['value'] : null;
                }
            }
            $dynamicModel->avatar = '/images/individual/avatar'.rand(1, 5).'.png';;
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
            $profile = Profile::find($id);
            if((!isset($profile))){
                return $this->success([], 'No profile record found to update', [], Response::HTTP_NO_CONTENT);
            }
            $old_value = Profile::findOrFail($id);
            $new_value = $request->all();

            $_dynamicModel = $profile->dynamicModel($request);
            Session::put('table_name', $profile->schema->name);
            $dynamicModel = new DynamicModel();
            $dynamicModel = $dynamicModel->where('id', $_dynamicModel->id)->first();

            foreach ($request->all() as $key => $value) {
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
     * Delete a Profile by IDssssssssssssss.
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
     * @throws Exception
     */
    public function delete($id) : Response
    {
        try {
            $profile = Profile::find($id);
            if((!isset($profile))){
                return $this->success([], 'No profile record found to delete', [], Response::HTTP_NOT_FOUND);
            }

            if ($profile->delete() === false) {
                throw new \RuntimeException('Could not delete the profile');
            }

            return response()->noContent();
        } catch (\Throwable $exception) {
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
     * @throws Exception
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
                $profileProcess = ProfileProcess::where('profile_id', $request->profile_id)->where('process_id', $request->process_id)->first();
                if (!isset($profileProcess->id) || !($profileProcess->id > 0)) {
                    $profileProcess = new ProfileProcess();
                    $profileProcess->profile_id = $data["profile_id"];
                    $profileProcess->process_id = $data["process_id"];

                    if ($profileProcess->save() === false) {
                        throw new \RuntimeException('Could not assign process to profile');
                    }

                    $processLog = new ProcessLog();
                    $processLog->profile_id = $data["profile_id"];
                    $processLog->process_id = $data["process_id"];
                    $processLog->process_status_id = ProcessStatus::ASSIGNED;
                    $processLog->step_id = 1;
                    $processLog->save();
                }
            }

            return $this->success(['id' => $profileProcess->id], 'process successfully assigned to profile.', $request->all(), Response::HTTP_CREATED);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to assign process to profile.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getSchema(Request $request, $id)
    {
        try {
            $schema = TenantSchema::where('dynamic_model_category_id', $id)->first();

            $request->merge(['table_name' => $schema->table_name]);
            $dynamicModel = new DynamicModel();

            $data['id'] = -1;
            // $data['validate'] = 1;
            $data['validate'] = 0;
            $data['schema_id'] = $schema->id;
            // $data['table_name'] = $schema->table_name;
            $data['table_name'] = $schema->name;
            // $data['steps'] = $dynamicModel->getSchema($schema->id);
            $data['steps'] = $dynamicModel->getSchemaByGroup($schema->id);

            return $this->success($data, 'profile schema successfully retrieved', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve profile schema.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function createProfile(ProfileRequest $request) : Response
    {
        try {
            $old_value = [];
            $new_value = $request->all();

            Session::put('table_name', $request->input('table_name'));
            $dynamicModel = new DynamicModel();
            $table_columns = DB::getSchemaBuilder()->getColumnListing($request->input('table_name'));
            foreach ($request->input('steps') as $key => $step) {
                foreach ($step['fields'] as $field) {
                    if (in_array($field['field'], $table_columns) && !in_array($field['field'], ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                        $dynamicModel->{$field['field']} = $field['value'];
                    }
                }
            }

            $profile = new Profile();
            $profile->schema_id = $request->input('schema_id');
            if ( (int)$request->input('profile_type_id') === 100 ) {
                $profile->avatar = '/images/individual/avatar' . rand(1, 5) . '.png';
                $profile->profile_type_id = 1;
            }
            if ( (int)$request->input('profile_type_id') === 200 ) {
                $profile->avatar = '/images/business/avatar' . rand(1, 5) . '.png';
                $profile->profile_type_id = 2;
            }
            $profile->save();

            // $dynamicModel->schema_id = $request->input('schema_id');
            // $dynamicModel->parent_id = $request->input('parent_id');
            $dynamicModel->parent_id = $profile->id;

            $dynamicModel->updated_at = now();

            if ($dynamicModel->save() === false) {
                throw new \RuntimeException('Could not create profile dynamic model');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to create the profile', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success(['id' => $profile->id], 'profile successfully created', $request->all(), Response::HTTP_CREATED, $old_value, $new_value);
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
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to delete process from profile.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
