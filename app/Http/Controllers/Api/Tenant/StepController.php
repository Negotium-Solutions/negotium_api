<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Exception;
use App\Http\Controllers\Throwable;
use App\Models\Tenant\Step;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

class StepController extends BaseApiController
{
    /**
     * Get step(s)
     *
     * @OA\Get(
     *       path="/{tenant}/step/{id}",
     *       summary="Get a Step",
     *       operationId="getStep",
     *       tags={"Step"},
     *       security = {{"BearerAuth": {}}},
     *       description="Authenticate using a bearer token",
     *       @OA\Parameter(name="id", description="Step Id", required=false, in="path", @OA\Schema( type="string" )),
     *       @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=500,description="Internal server error")
     *  ),
     *
     * @OA\Get(
     *       path="/{tenant}/step",
     *       summary="Get Steps",
     *       operationId="getSteps",
     *       tags={"Step"},
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
    public function get(Request $request, $parent_id, $id = null) : Response
    {
        try{
            $query = isset($id) ? Step::where('parent_id', $parent_id)->where('id', $id) : Step::where('parent_id', $parent_id);

            if ($request->has('with') && ($request->input('with') != '')) {
                $query = $query->with($request->with);
            }

            $data = isset($id) ? $query->first() : $query->get();

            if ((isset($id) && !isset($data)) || (!isset($id) && count($data) == 0)) {
                return $this->success([], 'No step record(s) found', [], Response::HTTP_NOT_FOUND);
            }

            return $this->success($data, 'steps(s) successfully retrieved', [], Response::HTTP_OK);
        }catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve.', [], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Create a new step.
     *
     * @OA\Post(
     *        path="/{tenant}/step/create",
     *        summary="Create a new step",
     *        operationId="createStep",
     *        tags={"Step"},
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
        $validator = Validator::make($request->all(),
            ['name' => 'string|required'],
            ['parent_id' => 'integer|required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNABLE_ENTITY);
        }

        try {
            $step = Step();
            $step->name = $request->name;
            $step->parent_id = $request->parent_id;

            if ($step->save() === false) {
                throw new \RuntimeException('Could not save step');
            }
            $step->oder = $step->id;
            $step->save();

            return $this->success(['id' => $step->id], 'Step successfully created.', $request->all(), Response::HTTP_CREATED);
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create step.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a step BY ID.
     *
     * @OA\Put(
     *        path="/{tenant}/step/update/{id}",
     *        summary="Update aStep",
     *        operationId="updateStep",
     *        tags={"Step"},
     *        security = {{"BearerAuth": {}}},
     *        description="Authenticate using a bearer token",
     *        @OA\Parameter(name="id", description="Step Id", required=true, in="path", @OA\Schema( type="string" )),
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
        $validator = Validator::make($request->all(),
            ['name' => 'string|required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $step = Step::find($id);
            if((!isset($step))){
                return $this->success([], 'No step record found to update', [], Response::HTTP_NOT_FOUND);
            }

            $old_value = Step::findOrFail($id);
            $new_value = $request->all();

            if ($step->updateOrFail($request->all()) === false) {
                throw new \RuntimeException('Could not update the step');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the step', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success([], 'Step successfully updated', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }

    /**
     * Delete a Step by ID.
     *
     * @OA\Delete(
     *      path="/{tenant}/step/delete/{id}",
     *      operationId="deleteStepById",
     *      tags={"Step"},
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
            $step = Step::find($id);
            if((!isset($step))){
                return $this->success([], 'No step record found to delete', [], Response::HTTP_NOT_FOUND);
            }

            if ($step->delete() === false) {
                throw new \RuntimeException('Could not delete the step');
            }

            return response()->noContent();
        } catch (Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the the step', ['id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
