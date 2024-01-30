<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Process;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

class ProcessController extends BaseAPIController
{
    /**
     * 
     * 
     * @OA\GET(
     *      path="/process/{id}",
     *      summary="Get a process",
     *      operationId="getProcess",
     *      tags={"process"},
     *      security = {{"BearerAuth": {}}},
     *      description="This can only be done by the logged in user.",
     *      @OA\Parameter(name="id",description="The process's id",required=true,in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(response=200,description="Successful operation",@OA\JsonContent()
     *       ),
     *      @OA\Response(response=401,description="Unauthenticated",
     *      )
     * ),
     * @OA\GET(
     *      path="/process",
     *      summary="Get processes",
     *      operationId="getProcesses",
     *      tags={"process"},
     *      security = {{"BearerAuth": {}}},
     *      description="This can only be done by the logged in user.",
     *      @OA\Response(response=200,description="Successful operation",@OA\JsonContent()
     *       ),
     *      @OA\Response(response=401,description="Unauthenticated",
     *      )
     * )
     * 
    */
    public function get(Request $request, $id = null) : Response
    {
        $query = isset($id) ? Process::find($id) : Process::query();

        $data = isset($id) ? $query : $query->get();

        return $this->success($data, 'processes successfully retrieved', [], Response::HTTP_OK);
    }

     /**
     * 
     * 
     * @OA\POST(
     *      path="/process/create",
     *      summary="Create a process",
     *      operationId="createProcess",
     *      tags={"process"},
     *      security = {{"BearerAuth": {}}},
     *      description="This can only be done by the logged in user.",
     *      @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="name",
     *                   type="string"
     *              )
     *          )
     *      )),
     *      @OA\Response(response=200,description="Successful operation",@OA\JsonContent()
     *       ),
     *      @OA\Response(response=401,description="Unauthenticated",
     *      ),
     * )
     * 
    */
    public function create(Request $request) : Response
    {
        $validator = \Validator::make($request->all(),
            ['name' => 'string|required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), 422);
        }

        try {
            $process = new Process();
            $process->name = $request->name;

            if ($process->save() === false) {
                throw new \RuntimeException('Could not save process');
            }

            return $this->success(['id' => $process->id], 'process successfully created.', $request->all(), 200);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create process.', []);
        }
    }

    /**
     * 
     * 
     *   @OA\PUT(
     *      path="/process/update/{id}",
     *      operationId="updateProcess",
     *      summary="Update process",
     *      tags={"process"},
     *      security = {{"BearerAuth": {}}},
     *      description="This can only be done by the logged in user.",
     *      @OA\Parameter(name="id",description="The process's id",required=true,in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(mediaType="application/json",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="name",
     *                   type="string",
     *              ),
     *           )
     *         )
     *      ),
     *      @OA\Response(response=200,description="Successful operation",@OA\JsonContent()
     *       ),
     *      @OA\Response(response=401,description="Unauthenticated",
     *      ),
     * )
     * 
    */
    public function update(Request $request, $id) : Response
    {
        $validator = \Validator::make($request->all(),
            ['name' => 'string|required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), 422);
        }

        try {
            $process = Process::findOrFail($id);
            $old_value = Process::findOrFail($id);
            $new_value = $request->all();

            if ($process->updateOrFail($request->all()) === false) {
                throw new \RuntimeException('Could not update the process');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the process', $request->all(), Response::HTTP_BAD_REQUEST);
        }

        return $this->success([], 'process successfully updated', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }

    /**
     * 
     * 
     * @OA\DELETE(
     *      path="/process/delete/{id}",
     *      operationId="deleteProcess",
     *      summary="Delete process",
     *      tags={"process"},
     *      security = {{"BearerAuth": {}}},
     *      description="This can only be done by the logged in user.",
     *      @OA\Parameter(
     *          name="id",
     *          description="The process's id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(response=200,description="Successful operation",@OA\JsonContent()
     *       ),
     *      @OA\Response(response=401,description="Unauthenticated",
     *      ),
     * )
     * 
    */
    public function delete($id) : Response
    {
        try {
            $process = Process::find($id);

            if ($process->delete() === false) {
                throw new \RuntimeException('Could not delete the process');
            }

            return $this->success([], 'process successfully deleted', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the the process', ['id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
