<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Requests\Tenant\ProcessCategoryRequest;
use App\Models\Tenant\ProcessCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;
use Throwable;

class ProcessCategoryController extends BaseAPIController
{
    /**
     * Get process categor(y)(ies)
     *
     * @OA\Get(
     *       path="/{tenant}/process-category/{id}",
     *       summary="Get a Process Category",
     *       operationId="getProcessCategory",
     *       tags={"Process Category"},
     *       security = {{"BearerAuth": {}}},
     *       description="Authenticate using a bearer token",
     *       @OA\Parameter(name="id", description="Process Category Id", required=false, in="path", @OA\Schema( type="string" )),
     *       @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=500,description="Internal server error")
     *  ),
     *
     * @OA\Get(
     *       path="/{tenant}/process-category",
     *       summary="Get Process Categories",
     *       operationId="getProcessCategories",
     *       tags={"Process Category"},
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
            $query = isset($id) ? ProcessCategory::where('id', $id) : ProcessCategory::query();

            if ($request->has('with')) {
                $query = $query->with($request->with);
            }

            $data = isset($id) ? $query->first() : $query->get();

            if ((isset($id) && !isset($data)) || (!isset($id) && count($data) == 0)) {
                return $this->success([], 'No process category record(s) found', [], Response::HTTP_NOT_FOUND);
            }

            return $this->success($data, 'process categories successfully retrieved', [], Response::HTTP_OK);
        }catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve process.', [], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Create a new process category.
     *
     * @OA\Post(
     *        path="/{tenant}/process-category/create",
     *        summary="Create a new process category",
     *        operationId="createProcessCategory",
     *        tags={"Process Category"},
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
    public function create(ProcessCategoryRequest $request) : Response
    {
        try {
            $processCategoryExists = ProcessCategory::where('name', $request->input('name'))->first();
            if (isset($processCategoryExists->id)) {
                return $this->success(['id' => $processCategoryExists->id, 'color' => $processCategoryExists->color], 'Process category with this name already exists.', $request->all(), Response::HTTP_CREATED);
            }

            $processCategory = new ProcessCategory();
            $processCategory->name = $request->name;
            $processCategory->color = fake()->colorName;

            if ($processCategory->save() === false) {
                throw new \RuntimeException('Could not save process category');
            }

            return $this->success(['id' => $processCategory->id, 'color' => $processCategory->color], 'process category successfully created.', $request->all(), Response::HTTP_CREATED);
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create process category.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a process category BY ID.
     *
     * @OA\Put(
     *        path="/{tenant}/process-category/update/{id}",
     *        summary="Update a ProcessCategory",
     *        operationId="updateProcessCategory",
     *        tags={"Process Category"},
     *        security = {{"BearerAuth": {}}},
     *        description="Authenticate using a bearer token",
     *        @OA\Parameter(name="id", description="Process Category Id", required=true, in="path", @OA\Schema( type="string" )),
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
            $processCategory = ProcessCategory::find($id);
            if((!isset($processCategory))){
                return $this->success([], 'No process category record found to update', [], Response::HTTP_NOT_FOUND);
            }

            $old_value = ProcessCategory::findOrFail($id);
            $new_value = $request->all();

            if ($processCategory->updateOrFail($request->all()) === false) {
                throw new \RuntimeException('Could not update the process category');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the process category', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success([], 'process category successfully updated', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }

    /**
     * Delete a Process Category by ID.
     *
     * @OA\Delete(
     *      path="/{tenant}/process-category/delete/{id}",
     *      operationId="deleteProcessCategoryById",
     *      tags={"Process Category"},
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
            $processCategory = ProcessCategory::find($id);
            if((!isset($processCategory))){
                return $this->success([], 'No process category record found to delete', [], Response::HTTP_NOT_FOUND);
            }

            if ($processCategory->delete() === false) {
                throw new \RuntimeException('Could not delete the process category');
            }

            return response()->noContent();
        } catch (Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the the process category', ['id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
