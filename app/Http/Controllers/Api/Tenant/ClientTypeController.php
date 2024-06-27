<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Controllers\Api\ApiInterface;
use App\Models\Tenant\ClientType;
use App\Services\SchemaService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

class ClientTypeController extends BaseAPIController implements ApiInterface
{
    public function __construct(protected SchemaService $schemaService)
    {
    }

    /**
     * Get client type(s)
     *
     * @OA\Get(
     *       path="/{tenant}/client-type/{id}",
     *       summary="Get a Client Type",
     *       operationId="getClientType",
     *       tags={"ClientType"},
     *       security = {{"BearerAuth": {}}},
     *       description="Authenticate using a bearer token",
     *       @OA\Parameter(name="id", description="Client Type Id", required=false, in="path", @OA\Schema( type="string" )),
     *       @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=500,description="Internal server error")
     *  ),
     *
     * @OA\Get(
     *       path="/{tenant}/client-type",
     *       summary="Get client types",
     *       operationId="getClientTypes",
     *       tags={"Client Type"},
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
            $query = isset($id) ? ClientType::where('id', $id) : ClientType::query();

            if ($request->has('with') && ($request->input('with') != '')) {
                $query = $query->with($request->with);
            }

            $data = isset($id) ? $query->first() : $query->get();

            if((isset($id) && !isset($data)) || (!isset($id) && count($data) == 0)){
                return $this->success([], 'No client type record(s) found', [], Response::HTTP_NOT_FOUND);
            }

            return $this->success($data, 'client types successfully retrieved', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve tenant.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new client type.
     *
     * @OA\Post(
     *        path="/{tenant}/client-type/create",
     *        summary="Create a new client type",
     *        operationId="createClientType",
     *        tags={"Client Type"},
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
            $clientType = new ClientType();
            $clientType->name = $request->name;

            if ($clientType->save() === false) {
                throw new \RuntimeException('Could not save client type');
            }

            // Create the schema here and link it to the activity
            $request->merge(['model' => 'ClientType']);
            $request->merge(['parent_id' => $clientType->id]);
            $this->schemaService->create($request);

            return $this->success(['id' => $clientType->id], 'Client type successfully created.', $request->all(), Response::HTTP_CREATED);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create client type.', [],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a client type BY ID.
     *
     * @OA\Put(
     *        path="/{tenant}/client-type/update/{id}",
     *        summary="Update a ClientType",
     *        operationId="updateClientType",
     *        tags={"Client Type"},
     *        security = {{"BearerAuth": {}}},
     *        description="Authenticate using a bearer token",
     *        @OA\Parameter(name="id", description="ClientType Id", required=true, in="path", @OA\Schema( type="string" )),
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
            $clientType = ClientType::find($id);
            if((!isset($clientType))){
                return $this->success([], 'No client type record found to update', [], Response::HTTP_NOT_FOUND);
            }
            $old_value = ClientType::findOrFail($id);
            $new_value = $request->all();

            if ($clientType->updateOrFail($request->all()) === false) {
                throw new \RuntimeException('Could not update client type');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the client type', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success([], 'client type successfully updated', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }

    /**
     * Delete a Client Type by ID.
     *
     * @OA\Delete(
     *      path="/{tenant}/client-type/delete/{id}",
     *      operationId="deleteClientTypeById",
     *      tags={"Client Type"},
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
            $clientType = ClientType::find($id);
            if((!isset($clientType))){
                return $this->success([], 'No client type type record found to delete', [], Response::HTTP_NOT_FOUND);
            }

            if ($clientType->delete() === false) {
                throw new \RuntimeException('Could not delete the client type');
            }

            return response()->noContent();
        } catch (\Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the client type', ['client_type_id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
