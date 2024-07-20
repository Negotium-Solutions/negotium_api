<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Controllers\Api\ApiInterface;
use App\Models\Tenant\ProfileType;
use App\Services\SchemaService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

class ProfileTypeController extends BaseAPIController implements ApiInterface
{
    public function __construct(protected SchemaService $schemaService)
    {
    }

    /**
     * Get profile type(s)
     *
     * @OA\Get(
     *       path="/{tenant}/profile-type/{id}",
     *       summary="Get a Profile Type",
     *       operationId="getProfileType",
     *       tags={"ProfileType"},
     *       security = {{"BearerAuth": {}}},
     *       description="Authenticate using a bearer token",
     *       @OA\Parameter(name="id", description="Profile Type Id", required=false, in="path", @OA\Schema( type="string" )),
     *       @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=500,description="Internal server error")
     *  ),
     *
     * @OA\Get(
     *       path="/{tenant}/profile-type",
     *       summary="Get profile types",
     *       operationId="getProfileTypes",
     *       tags={"ProfileType"},
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
            $query = isset($id) ? ProfileType::where('id', $id) : ProfileType::query();

            if ($request->has('with') && ($request->input('with') != '')) {
                $_with = explode(',', $request->input('with'));
                $query = $query->with($_with);
            }

            $data = isset($id) ? $query->first() : $query->get();

            if((isset($id) && !isset($data)) || (!isset($id) && count($data) == 0)){
                return $this->success([], 'No profile type record(s) found', [], Response::HTTP_NOT_FOUND);
            }

            return $this->success($data, 'profile types successfully retrieved', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve tenant.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new profile type.
     *
     * @OA\Post(
     *        path="/{tenant}/profile-type/create",
     *        summary="Create a new profile type",
     *        operationId="createProfileType",
     *        tags={"ProfileType"},
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
            'name' => 'string|required',
            'columns' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $profileType = new ProfileType();
            $profileType->name = $request->name;

            if ($profileType->save() === false) {
                throw new \RuntimeException('Could not save profile type');
            }

            // Create the schema here and link it to the activity
            $request->merge(['model' => 'ProfileType']);
            $request->merge(['parent_id' => $profileType->id]);
            $this->schemaService->create($request);

            return $this->success(['id' => $profileType->id], 'Profile type successfully created.', $request->all(), Response::HTTP_CREATED);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create profile type.', [],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a profile type BY ID.
     *
     * @OA\Put(
     *        path="/{tenant}/profile-type/update/{id}",
     *        summary="Update a ProfileType",
     *        operationId="updateProfileType",
     *        tags={"ProfileType"},
     *        security = {{"BearerAuth": {}}},
     *        description="Authenticate using a bearer token",
     *        @OA\Parameter(name="id", description="ProfileType Id", required=true, in="path", @OA\Schema( type="string" )),
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
        $validator = \Validator::make($request->all(),
            ['name' => 'string']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $profileType = ProfileType::find($id);
            if((!isset($profileType))){
                return $this->success([], 'No profile type record found to update', [], Response::HTTP_NOT_FOUND);
            }
            $old_value = ProfileType::findOrFail($id);
            $new_value = $request->all();

            if ($profileType->updateOrFail($request->all()) === false) {
                throw new \RuntimeException('Could not update profile type');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the profile type', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success([], 'profile type successfully updated', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }

    /**
     * Delete a Profile Type by ID.
     *
     * @OA\Delete(
     *      path="/{tenant}/profile-type/delete/{id}",
     *      operationId="deleteProfileTypeById",
     *      tags={"ProfileType"},
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
            $profileType = ProfileType::find($id);
            if((!isset($profileType))){
                return $this->success([], 'No profile type type record found to delete', [], Response::HTTP_NOT_FOUND);
            }

            if ($profileType->delete() === false) {
                throw new \RuntimeException('Could not delete the profile type');
            }

            return response()->noContent();
        } catch (\Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the profile type', ['profile_type_id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
