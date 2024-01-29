<?php
namespace App\Http\Controllers\Api;

use App\Models\ProcessCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

class ProcessCategoryController extends BaseAPIController
{
    /**
     * 
     * 
     * @OA\GET(
     *      path="/process-category/{id}",
     *      summary="Get a process category",
     *      operationId="getProcess-category",
     *      tags={"process-category"},
     *      security = {{"BearerAuth": {}}},
     *      description="This can only be done by the logged in user.",
     *      @OA\Parameter(
     *          name="id",
     *          description="The process category's id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *          
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     * ),
     * @OA\GET(
     *      path="/process-category",
     *      summary="Get process categories",
     *      operationId="getProcess-categories",
     *      tags={"process-category"},
     *      security = {{"BearerAuth": {}}},
     *      description="This can only be done by the logged in user.",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      )
     * )
     * 
    */
    public function get(Request $request, $id = null) : Response
    {
        $query = isset($id) ? ProcessCategory::find($id) : ProcessCategory::query();

        $data = isset($id) ? $query : $query->get();

        return $this->success($data, 'process categories successfully retrieved', [], Response::HTTP_OK);
    }

   /**
     * 
     * 
     * @OA\POST(
     *      path="/process-category/create",
     *      operationId="createProcess-category",
     *      summary="Get a process category",
     *      tags={"process-category"},
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
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
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
            $processCategory = new ProcessCategory();
            $processCategory->name = $request->name;

            if ($processCategory->save() === false) {
                throw new \RuntimeException('Could not save process category');
            }

            return $this->success(['id' => $processCategory->id], 'process category successfully created.', $request->all(), 200);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create process category.', []);
        }
    }

    /**
     * 
     * 
     * @OA\PUT(
     *      path="/process-category/update/{id}",
     *      operationId="updateProcess-category",
     *      summary="Update process category",
     *      tags={"process-category"},
     *      security = {{"BearerAuth": {}}},
     *      description="This can only be done by the logged in user.",
     *      @OA\Parameter(
     *          name="id",
     *          description="The process category's id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="name",
     *                   type="string",
     *              ),
     *           )
     *         )
     *      ),
     *      @OA\Response(
     *         response=200,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
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
            $processCategory = ProcessCategory::findOrFail($id);
            $old_value = ProcessCategory::findOrFail($id);
            $new_value = $request->all();

            if ($processCategory->updateOrFail($request->all()) === false) {
                throw new \RuntimeException('Could not update the process category');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the process category', $request->all(), Response::HTTP_BAD_REQUEST);
        }

        return $this->success([], 'process category successfully updated', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }

    /**
     * 
     * 
     * @OA\DELETE(
     *      path="/process-category/delete/{id}",
     *      operationId="deleteProcess-category",
     *      summary="Delete process category",
     *      tags={"process-category"},
     *      security = {{"BearerAuth": {}}},
     *      description="This can only be done by the logged in user.",
     *      @OA\Parameter(
     *          name="id",
     *          description="The process category's id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *         response=200,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     * )
     * 
    */
    public function delete($id) : Response
    {
        try {
            $processCategory = ProcessCategory::find($id);

            if ($processCategory->delete() === false) {
                throw new \RuntimeException('Could not delete the process category');
            }

            return $this->success([], 'process category successfully deleted', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the the process category', ['id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
