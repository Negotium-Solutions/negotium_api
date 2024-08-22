<?php

namespace App\Http\Controllers\Api\Tenant;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;
use App\Models\Tenant\CommunicationTy;

class LookUpController extends BaseApiController
{
    private string $modelPath;

    public function __construct()
    {
        $this->modelPath = '\\App\\Models\\Tenant\\';
    }

    /**
     * Get lookup table
     *
     * @OA\Get(
     *       path="/{tenant}/lookup",
     *       summary="Get a Lookup",
     *       operationId="getLookUp",
     *       tags={"Lookup"},
     *       security = {{"BearerAuth": {}}},
     *       description="Authenticate using a bearer token",
     *       @OA\Parameter(name="id", description="Communication Id", required=false, in="path", @OA\Schema( type="string" )),
     *       @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=500,description="Internal server error")
     *  )
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function get(Request $request) : Response
    {
        $validator = \Validator::make($request->all(), [
            'model' => 'string'
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error, model is required', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $model = $this->modelPath . $request->input('model');

            $query = $model::orderBy('name');

            $data = isset($request->object) && $request->object === 1 ? $query->get() : $query->pluck('name', 'id');

            if (count($data) == 0) {
                return $this->success([], "No lookup record(s) found", [], Response::HTTP_NOT_FOUND);
            }

            return $this->success($data, 'lookup successfully retrieved', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve lookup.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
