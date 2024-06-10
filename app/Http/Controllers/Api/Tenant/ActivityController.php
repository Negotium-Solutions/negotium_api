<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Models\Tenant\Activity;
use App\Models\Tenant\Step;
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
     *       path="/{tenant}/activity/{schema_id}/{id}",
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
     *       path="/{tenant}/activity/{schema_id}",
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
     * @param integer $schema_id
     * @param integer $id
     * @return Response
     * @throws Exception
     */
    public function get(Request $request, int $step_id, int $id = null) : Response
    {
        try{
            $query = isset($id) ? Activity::where('step_id', $step_id)->where('id', $id) : Activity::where('step_id', $step_id);

            if ($request->has('with') && ($request->input('with') != '')) {
                $query = $query->with($request->with);
            }

            $data = isset($id) ? $query->first() : $query->get();

            if((isset($id) && !isset($data)) || (!isset($id) && count($data) == 0)){
                return $this->success([], 'No activity record(s) found', [], Response::HTTP_NOT_FOUND);
            }

            return $this->success($data, 'Activities successfully retrieved', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve activities.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new activity.
     *
     * @OA\Post(
     *        path="/{tenant}/activity/create/{step}",
     *        summary="Create a new activity",
     *        operationId="createActivity",
     *        tags={"Activity"},
     *        security = {{"BearerAuth": {}}},
     *        description="Authenticate using a bearer token",
     *        @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={"process_step_id":2,"name":"activity","columns":{{"name":"name","type":"string","attributes":{"required":1,"indent":0,"is_id":0,"is_passport":0}},{"name":"email","type":"string","attributes":{"required":1,"indent":0,"is_id":0,"is_passport":0}},{"name":"age","type":"integer","attributes":{"required":1,"indent":0,"is_id":0,"is_passport":0}},{"name":"biography","type":"text","attributes":{"required":1,"indent":0,"is_id":0,"is_passport":0}}}}
     *              )
     *          )
     *        ),
     *        @OA\Response(response=200,description="Successful operation",@OA\JsonContent(
     *            @OA\Property(property="data", type="object", example={"code":201,"message":"Activity successfully created.","data":{"id":7}}),
     *        )),
     *        @OA\Response(response=422,description="Input validation error"),
     *        @OA\Response(response=500,description="Internal server error")
     * )
     *
     * @param Request $request
     * @param integer $request
     * @return Response
     * @throws Exception
     */
    public function create(Request $request, Step $step) : Response
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'string|required',
            'label' => 'string|required',
            'type_id' => 'integer|required',
            'attributes' => 'string'
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $activity = new Activity();
            $activity->name = $request->input('name');
            $activity->label = $request->input('label');
            $activity->type_id = $request->input('type_id');
            $activity->step_id = $step->id;
            $activity->attributes = $request->input('attributes');

            if ($activity->save() === false) {
                throw new \RuntimeException('Could not save activity');
            }

            $activity->name .= '_'.$activity->id;
            $activity->save();

            if (!isset($step->schema->id)) {
                $this->schemaService->create($step);
            }

            $this->schemaService->addColumn($activity);

            return $this->success(['id' => $activity->id], 'Activity successfully created.', $request->all(), Response::HTTP_CREATED);
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
    public function update(Request $request, int $id) : Response
    {
        $validator = \Validator::make($request->all(), [
            'label' => 'string|required',
            'attributes' => 'string'
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $activity = Activity::find($id);
            $activity->label = $request->input('label');
            $activity->attributes = $request->input('attributes');
            $activity->save();

            $this->schemaService->updateColumn($activity);
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the activity', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success([], 'activity successfully updated', $request->all(), Response::HTTP_OK, [], []);
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
    public function delete(int $id) : Response
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
