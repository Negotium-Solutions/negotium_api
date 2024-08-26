<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Models\Tenant\DynamicModel;
use App\Models\Tenant\DynamicModelField;
use App\Models\Tenant\ProcessLog;
use App\Models\Tenant\ProcessStatus;
use App\Models\Tenant\Profile;
use App\Models\Tenant\ProfileProcess;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
        try{
            $query = isset($id) ? Profile::where('id', $id) : Profile::query();

            if ($request->has('pt_id') && ($request->input('pt_id') > 0)) {
                $query = $query->where('profile_type_id', $request->input('pt_id'));
            }

            $hasDynamicModel = false;
            if ($request->has('with') && ($request->input('with') != '')) {
                $_with = explode(',', $request->input('with'));

                if (in_array('dynamicModel', $_with)) {
                    $hasDynamicModel = true;
                    $index = array_search('dynamicModel', $_with);
                    unset($_with[$index]);
                }
                $query = $query->with($_with);
            }

            $data = isset($id) ? $query->first() : $query->get();

            if (isset($id) && $hasDynamicModel) {
                $profile = Profile::find($id);
                $data['dynamicModel'] = $profile->dynamicModel()->toArray();
                $data['dynamicModelFields'] = $profile->dynamicModelFields();
                /*$data['dynamicModelFields'] = DynamicModelField::with('dynamicModelFieldGroup')
                    ->where('schema_id', $profile->schema_id)
                    ->orderBy('dynamic_model_field_group_id')->get();*/
            }

            if((isset($id) && !isset($data)) || (!isset($id) && count($data) == 0)){
                return $this->success([], 'No profile record(s) found', [], Response::HTTP_NOT_FOUND);
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
    public function create(Request $request) : Response
    {
        $validator = \Validator::make($request->all(), [
            'first_name' => 'string|required',
            'last_name' => 'string|required'
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $profile = new Profile();
            $profile->first_name = $request->first_name;
            $profile->last_name = $request->last_name;

            if ($profile->save() === false) {
                throw new \RuntimeException('Could not save profile');
            }

            return $this->success(['id' => $profile->id], 'profile successfully created.', $request->all(), Response::HTTP_CREATED);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create profile.', [],  Response::HTTP_INTERNAL_SERVER_ERROR);
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
    public function update(Request $request, $id) : Response
    {
        $validator = \Validator::make($request->all(), [
            // Todo: implement dynamic validation from field assigned attributes
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $profile = Profile::find($id);
            if((!isset($profile))){
                return $this->success([], 'No profile record found to update', [], Response::HTTP_NO_CONTENT);
            }
            $old_value = Profile::findOrFail($id);
            $new_value = $request->all();

            if ($profile->updateOrFail($request->all()) === false) {
                throw new \RuntimeException('Could not update profile');
            }

            $_dynamicModelRequest = $request->input('dynamicModel');
            $dynamicModel = new DynamicModel();
            $dynamicModel->setTable($profile->schema->name);
            $dynamicModel = $dynamicModel->where('id', $_dynamicModelRequest['id'])->first();
            foreach ($_dynamicModelRequest as $key => $value) {
                if ($key != 'id') {
                    $dynamicModel->{$key} = $value;
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
            foreach($request->input('data') as $data) {
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
            return $this->error($exception->getMessage(), 'An error occurred while trying to assign process to profile.', [],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
