<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Models\Tenant\Schema;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

class DynamicModelController extends BaseApiController
{
    /**
     * Get profile(s)
     *
     * @OA\Get(
     *       path="/{tenant}/dynamic-model/{id}",
     *       summary="Get a Dynamic Models",
     *       operationId="getDynamicModel",
     *       tags={"DynamicModel"},
     *       security = {{"BearerAuth": {}}},
     *       description="Authenticate using a bearer token",
     *       @OA\Parameter(name="id", description="Dynamic Model Id", required=false, in="path", @OA\Schema( type="string" )),
     *       @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=500,description="Internal server error")
     *  ),
     *
     * @OA\Get(
     *       path="/{tenant}/dynamic-model",
     *       summary="Get dynamic models",
     *       operationId="getDynamicModels",
     *       tags={"DynamicModel"},
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
            $query = isset($id) ? Schema::where('id', $id) : Schema::query();

            if ($request->has('with') && ($request->input('with') != '')) {
                $_with = explode(',', $request->input('with'));
                $query = $query->with($_with);
            }

            if ($request->has('type_id') && ($request->input('type_id') > 0)) {
                $query = $query->where('dynamic_model_type_id', $request->input('type_id'));
            }

            $data = isset($id) ? $query->first() : $query->get();

            if((isset($id) && !isset($data)) || (!isset($id) && count($data) == 0)){
                return $this->success([], 'No dynamic models record(s) found', [], Response::HTTP_NOT_FOUND);
            }

            return $this->success($data, 'dynamic models successfully retrieved', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve dynamic model(s).', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
