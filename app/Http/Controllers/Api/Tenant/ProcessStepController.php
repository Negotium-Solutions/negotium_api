<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Models\Tenant\ProcessStep;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ProcessStepController extends BaseAPIController
{
    /**
     * Get process step(s)
     *
     * @OA\Get(
     *       path="/{tenant}/process-step/{id}",
     *       summary="Get a Process Step",
     *       operationId="getProcessStep",
     *       tags={"Process Step"},
     *       security = {{"BearerAuth": {}}},
     *       description="Authenticate using a bearer token",
     *       @OA\Parameter(name="id", description="Process Category Id", required=false, in="path", @OA\Schema( type="string" )),
     *       @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=500,description="Internal server error")
     *  ),
     *
     * @OA\Get(
     *       path="/{tenant}/process-step",
     *       summary="Get Process Steps",
     *       operationId="getProcessSteps",
     *       tags={"Process Step"},
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
            $query = isset($id) ? ProcessStep::where('id', $id) : ProcessStep::query();

            if ($request->has('with') && ($request->input('with') != '')) {
                $query = $query->with($request->with);
            }

            $data = isset($id) ? $query->first() : $query->get();

            if ((isset($id) && !isset($data)) || (!isset($id) && count($data) == 0)) {
                return $this->success([], 'No process step record(s) found', [], Response::HTTP_NOT_FOUND);
            }

            return $this->success($data, 'process steps(s) successfully retrieved', [], Response::HTTP_OK);
        }catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve process.', [], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Create a new process step.
     *
     * @OA\Post(
     *        path="/{tenant}/process-step/create",
     *        summary="Create a new process step",
     *        operationId="createProcessStep",
     *        tags={"Process Step"},
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
            ['process_id' => 'integer|required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $processStep = new ProcessStep();
            $processStep->name = $request->name;
            $processStep->process_id = $request->process_id;

            if ($processStep->save() === false) {
                throw new \RuntimeException('Could not save process step');
            }

            return $this->success(['id' => $processStep->id], 'process step successfully created.', $request->all(), Response::HTTP_CREATED);
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create process step.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a process step BY ID.
     *
     * @OA\Put(
     *        path="/{tenant}/process-step/update/{id}",
     *        summary="Update a ProcessStep",
     *        operationId="updateProcessStep",
     *        tags={"Process Step"},
     *        security = {{"BearerAuth": {}}},
     *        description="Authenticate using a bearer token",
     *        @OA\Parameter(name="id", description="Process Step Id", required=true, in="path", @OA\Schema( type="string" )),
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
            $processStep = ProcessStep::find($id);
            if((!isset($processStep))){
                return $this->success([], 'No process step record found to update', [], Response::HTTP_NOT_FOUND);
            }

            $old_value = ProcessStep::findOrFail($id);
            $new_value = $request->all();

            if ($processStep->updateOrFail($request->all()) === false) {
                throw new \RuntimeException('Could not update the process step');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the process step', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success([], 'process step successfully updated', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }

    /**
     * Delete a Process Step by ID.
     *
     * @OA\Delete(
     *      path="/{tenant}/process-step/delete/{id}",
     *      operationId="deleteProcessStepById",
     *      tags={"Process Step"},
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
            $processStep = ProcessStep::find($id);
            if((!isset($processStep))){
                return $this->success([], 'No process step record found to delete', [], Response::HTTP_NOT_FOUND);
            }

            if ($processStep->delete() === false) {
                throw new \RuntimeException('Could not delete the process step');
            }

            return response()->noContent();
        } catch (Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the the process step', ['id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
