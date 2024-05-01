<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Models\Tenant\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

class ClientController extends BaseAPIController
{
    /**
     * Get client(s)
     *
     * @OA\Get(
     *       path="/{tenant}/client/{id}",
     *       summary="Get a Client",
     *       operationId="getClient",
     *       tags={"Client"},
     *       security = {{"BearerAuth": {}}},
     *       description="Authenticate using a bearer token",
     *       @OA\Parameter(name="id", description="Client Id", required=false, in="path", @OA\Schema( type="string" )),
     *       @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=500,description="Internal server error")
     *  ),
     *
     * @OA\Get(
     *       path="/{tenant}/client",
     *       summary="Get clients",
     *       operationId="getClients",
     *       tags={"Client"},
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
            $query = isset($id) ? Client::where('id', $id) : Client::query();

            $data = isset($id) ? $query->first() : $query->get();

            if((isset($id) && !isset($data)) || (!isset($id) && count($data) == 0)){
                return $this->success([], 'No client record(s) found', [], Response::HTTP_NOT_FOUND);
            }

            return $this->success($data, 'clients successfully retrieved', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve tenant.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new client.
     *
     * @OA\Post(
     *        path="/{tenant}/client/create",
     *        summary="Create a new client",
     *        operationId="createClient",
     *        tags={"Client"},
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
            ['first_name' => 'string|required'],
            ['last_name' => 'string|required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $client = new Client();
            $client->first_name = $request->first_name;
            $client->last_name = $request->last_name;

            if ($client->save() === false) {
                throw new \RuntimeException('Could not save client');
            }

            return $this->success(['id' => $client->id], 'client successfully created.', $request->all(), Response::HTTP_CREATED);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create client.', [],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a client BY ID.
     *
     * @OA\Put(
     *        path="/{tenant}/client/update/{id}",
     *        summary="Update a Client",
     *        operationId="updateClient",
     *        tags={"Client"},
     *        security = {{"BearerAuth": {}}},
     *        description="Authenticate using a bearer token",
     *        @OA\Parameter(name="id", description="Client Id", required=true, in="path", @OA\Schema( type="string" )),
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
            ['first_name' => 'string'],
            ['last_name' => 'string']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $client = Client::find($id);
            if((!isset($client))){
                return $this->success([], 'No client record found to update', [], Response::HTTP_NOT_FOUND);
            }
            $old_value = Client::findOrFail($id);
            $new_value = $request->all();

            if ($client->updateOrFail($request->all()) === false) {
                throw new \RuntimeException('Could not update client');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the client', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success([], 'client successfully updated', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }

    /**
     * Delete a Client by ID.
     *
     * @OA\Delete(
     *      path="/{tenant}/client/delete/{id}",
     *      operationId="deleteClientById",
     *      tags={"Client"},
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
            $client = Client::find($id);
            if((!isset($client))){
                return $this->success([], 'No client record found to delete', [], Response::HTTP_NOT_FOUND);
            }

            if ($client->delete() === false) {
                throw new \RuntimeException('Could not delete the client');
            }

            return response()->noContent();
        } catch (\Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the client', ['client_id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
