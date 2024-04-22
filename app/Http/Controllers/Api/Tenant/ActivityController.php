<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Models\Tenant\Activity;
use App\Services\SchemaService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

class ActivityController extends BaseAPIController
{
    public function __construct(protected SchemaService $schemaService)
    {
    }

    /**
     * Get activit(y)/(ies)
     *
     * @OA\Get(
     *       path="/{tenant}/activity/{id}",
     *       summary="Get a activity",
     *       operationId="getActivity",
     *       tags={"Activity"},
     *       security = {{"BearerAuth": {}}},
     *       description="Authenticate using a bearer token",
     *       @OA\Parameter(name="id", description="Activity Id", required=false, in="path", @OA\Schema( type="string" )),
     *       @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=500,description="Internal server error")
     *  ),
     *
     * @OA\Get(
     *       path="/{tenant}/activity",
     *       summary="Get Activities",
     *       operationId="getActivities",
     *       tags={"Activity"},
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
    public function get(Request $request, int $id = null) : Response
    {
        try{
            $query = isset($id) ? Activity::find($id) : Activity::query();

            $data = isset($id) ? $query : $query->get();

            if((isset($id) && !isset($data)) || (!isset($id) && count($data) == 0)){
                return $this->success([], 'No activity record(s) found', [], Response::HTTP_NOT_FOUND);
            }

            return $this->success($data, 'activities successfully retrieved', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve tenant.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new activity.
     *
     * @OA\Post(
     *        path="/{tenant}/activity/create",
     *        summary="Create a new activity",
     *        operationId="createActivity",
     *        tags={"Activity"},
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
        $validator = \Validator::make($request->all(),
            ['step_id' => 'integer|required'],
            ['name' => 'string|required'],
            ['columns' => 'required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $activity = new Activity();
            $activity->process_step_id = $request->step_id;

            if ($activity->save() === false) {
                throw new \RuntimeException('Could not save activity');
            }

            // Create the schema here and link it to the activity
            $request->merge(['model' => 'Activity']);
            $request->merge(['parent_id' => $activity->id]);
            $this->schemaService->create($request);

            return $this->success(['id' => $activity->id], 'activity successfully created.', $request->all(), Response::HTTP_CREATED);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create activity.', [],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a activity BY ID.
     *
     * @OA\Put(
     *        path="/{tenant}/activity/update/{id}",
     *        summary="Update a activity",
     *        operationId="updateActivity",
     *        tags={"Activity"},
     *        security = {{"BearerAuth": {}}},
     *        description="Authenticate using a bearer token",
     *        @OA\Parameter(name="id", description="Activity Id", required=true, in="path", @OA\Schema( type="string" )),
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
            ['email' => 'email']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $activity = Activity::find($id);
            if((!isset($activity))){
                return $this->success([], 'No activity record found to update', [], Response::HTTP_NOT_FOUND);
            }
            $old_value = Activity::findOrFail($id);
            $new_value = $request->all();

            if ($activity->updateOrFail($request->all()) === false) {
                throw new \RuntimeException('Could not update activity');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the activity', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success([], 'activity successfully updated', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }

    /**
     * Delete a activity by ID.
     *
     * @OA\Delete(
     *      path="/{tenant}/activity/delete/{id}",
     *      operationId="deleteActivityById",
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
    public function delete($id) : Response
    {
        try {
            $activity = Activity::find($id);
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
