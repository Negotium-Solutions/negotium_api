<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Controllers\Api\ApiInterface;
use App\Models\Tenant\Attribute;
use App\Models\Tenant\Form;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

class FormController extends BaseAPIController implements ApiInterface
{
    /**
     * Get form(s)
     *
     * @OA\Get(
     *       path="/{tenant}/form/{id}",
     *       summary="Get a form",
     *       operationId="getForm",
     *       tags={"Form"},
     *       security = {{"BearerAuth": {}}},
     *       description="Authenticate using a bearer token",
     *       @OA\Parameter(name="id", description="Form Id", required=false, in="path", @OA\Schema( type="string" )),
     *       @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=500,description="Internal server error")
     *  ),
     *
     * @OA\Get(
     *       path="/{tenant}/form",
     *       summary="Get forms",
     *       operationId="getForms",
     *       tags={"Form"},
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
            $form = Form::with('steps.activities')->where('id', $id)->first();

            $data['activities'] = $form->steps[0]->activities;
            $data['attributes'] = Attribute::orderBy('id')->get();

            if(count($data['activities']) == 0){
                return $this->success([], 'No form record(s) found', [], Response::HTTP_NOT_FOUND);
            }

            return $this->success($data, 'forms successfully retrieved', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve forms.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new form.
     *
     * @OA\Post(
     *        path="/{tenant}/form/create",
     *        summary="Create a new form",
     *        operationId="createForm",
     *        tags={"Form"},
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
            'name' => 'string|required'
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $form = new Form();
            $form->name = $request->name;

            if ($form->save() === false) {
                throw new \RuntimeException('Could not save form');
            }

            return $this->success(['id' => $form->id], 'form successfully created.', $request->all(), Response::HTTP_CREATED);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create form.', [],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a form BY ID.
     *
     * @OA\Put(
     *        path="/{tenant}/form/update/{id}",
     *        summary="Update a Form",
     *        operationId="updateForm",
     *        tags={"Form"},
     *        security = {{"BearerAuth": {}}},
     *        description="Authenticate using a bearer token",
     *        @OA\Parameter(name="id", description="Form Id", required=true, in="path", @OA\Schema( type="string" )),
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
            'name' => 'string|required'
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $form = Form::find($id);
            if((!isset($form))){
                return $this->success([], 'No form record found to update', [], Response::HTTP_NOT_FOUND);
            }
            $old_value = Form::findOrFail($id);
            $new_value = $request->all();

            $form->name = $request->name;

            if ($form->save() === false) {
                throw new \RuntimeException('Could not update form');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the form', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success([], 'form successfully updated', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }

    /**
     * Delete a Form by ID.
     *
     * @OA\Delete(
     *      path="/{tenant}/form/delete/{id}",
     *      operationId="deleteFormById",
     *      tags={"Form"},
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
            $form = Form::find($id);
            if((!isset($form))){
                return $this->success([], 'No form record found to delete', [], Response::HTTP_NOT_FOUND);
            }

            if ($form->delete() === false) {
                throw new \RuntimeException('Could not delete the form');
            }

            return response()->noContent();
        } catch (\Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the form', ['form_id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
