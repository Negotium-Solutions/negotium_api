<?php

namespace App\Http\Controllers\Api\Tenant;

use Throwable;
use App\Models\Tenant\Process;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;
use Illuminate\Support\Facades\Validator;

class ProcessController extends BaseAPIController
{
    /**
     * Get process categor(y)(ies)
     *
     * @OA\Get(
     *       path="/{tenant}/process/{id}",
     *       summary="Get a Process",
     *       operationId="getProcess",
     *       tags={"Process"},
     *       security = {{"BearerAuth": {}}},
     *       description="Authenticate using a bearer token",
     *       @OA\Parameter(name="id", description="Process Category Id", required=false, in="path", @OA\Schema( type="string" )),
     *       @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=500,description="Internal server error")
     *  ),
     *
     * @OA\Get(
     *       path="/{tenant}/process",
     *       summary="Get Processes",
     *       operationId="getProcesses",
     *       tags={"Process"},
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
            $query = isset($id) ? Process::where('id', $id) : Process::query();

            if ($request->has('with')) {
                $with_array = explode(',', $request->with);
                $query = $query->with($with_array);
            }

            $data = isset($id) ? $query->first() : $query->get();

            if((isset($id) && !isset($data)) || (!isset($id) && count($data) == 0)){
                return $this->success([], 'No process record(s) found', [], Response::HTTP_NOT_FOUND);
            }

            return $this->success($data, 'processes successfully retrieved', [], Response::HTTP_OK);
        }catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve process.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new process category.
     *
     * @OA\Post(
     *        path="/{tenant}/process/create",
     *        summary="Create a new process",
     *        operationId="createProcess",
     *        tags={"Process"},
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
            ['process_category_id' => 'integer|required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(),  Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $process = new Process();
            $process->name = $request->name;
            $process->process_category_id = $request->process_category_id;

            if ($process->save() === false) {
                throw new \RuntimeException('Could not save process');
            }

            return $this->success(['id' => $process->id], 'process successfully created.', $request->all(),  Response::HTTP_CREATED);
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create process.', [],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a process category BY ID.
     *
     * @OA\Put(
     *        path="/{tenant}/process/update/{id}",
     *        summary="Update a Process",
     *        operationId="updateProcess",
     *        tags={"Process"},
     *        security = {{"BearerAuth": {}}},
     *        description="Authenticate using a bearer token",
     *        @OA\Parameter(name="id", description="Process Id", required=true, in="path", @OA\Schema( type="string" )),
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
            $process = Process::find($id);
            if((!isset($process))){
                return $this->success([], 'No process record found to update', [], Response::HTTP_NOT_FOUND);
            }

            $old_value = Process::findOrFail($id);
            $new_value = $request->all();

            if ($process->updateOrFail($request->all()) === false) {
                throw new \RuntimeException('Could not update the process');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the process', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success([], 'process successfully updated', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }

    /**
     * Delete a Process by ID.
     *
     * @OA\Delete(
     *      path="/{tenant}/process/delete/{id}",
     *      operationId="deleteProcessById",
     *      tags={"Process"},
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
            $process = Process::find($id);
            if((!isset($process))){
                return $this->success([], 'No process record found to delete', [], Response::HTTP_NOT_FOUND);
            }

            if ($process->delete() === false) {
                throw new \RuntimeException('Could not delete the process');
            }

            return response()->noContent();
        } catch (Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the the process', ['id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
