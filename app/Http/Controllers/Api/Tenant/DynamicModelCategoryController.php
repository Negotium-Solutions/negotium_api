<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Models\Tenant\DynamicModelCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

class DynamicModelCategoryController extends BaseApiController
{
    /**
     * Get dynamic model categor(y)(ies)
     *
     * @OA\Get(
     *       path="/{tenant}/dynamic-model-category/{id}",
     *       summary="Get a Dynamic Model Category",
     *       operationId="getDynamicModelCategory",
     *       tags={"DynamicModelCategory"},
     *       security = {{"BearerAuth": {}}},
     *       description="Authenticate using a bearer token",
     *       @OA\Parameter(name="id", description="Dynamic Model Category Category Id", required=false, in="path", @OA\Schema( type="string" )),
     *       @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=500,description="Internal server error")
     *  ),
     *
     * @OA\Get(
     *       path="/{tenant}/dynamic-model-category",
     *       summary="Get Dynamic Model Categories",
     *       operationId="DynamicModelCategories",
     *       tags={"Dynamic Model Category"},
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
            $query = isset($id) ? DynamicModelCategory::where('id', $id) : DynamicModelCategory::query();

            if ($request->has('with') && $request->input('with') != '') {
                $with_array = explode(',', $request->with);
                $query = $query->with($with_array);
            }

            if ( $request->has('dynamic_model_type_id') && $request->input('dynamic_model_type_id') > 0 ) {
                $query = $query->where('dynamic_model_type_id', $request->get('dynamic_model_type_id'));
            }

            $data = isset($id) ? $query->first() : $query->get();

            if((isset($id) && !isset($data)) || (!isset($id) && count($data) == 0)){
                return $this->success([], 'No dynamic model category record(s) found', [], Response::HTTP_NOT_FOUND);
            }

            return $this->success($data, 'dynamic model categories successfully retrieved', [], Response::HTTP_OK);
        }catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve dynamic model category.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
