<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Models\Tenant\ActivityGroup;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

class ActivityGroupController extends BaseApiController
{
    /**
     * Get activity group(s)
     *
     * @OA\Get(
     *       path="/{tenant}/activity-group/{id}",
     *       summary="Get a activity group",
     *       operationId="getActivityGroup",
     *       tags={"ActivityGroup"},
     *       security = {{"BearerAuth": {}}},
     *       description="Authenticate using a bearer token",
     *       @OA\Parameter(name="id", description="Activity Group Id", required=false, in="path", @OA\Schema( type="string" )),
     *       @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=500,description="Internal server error")
     *  ),
     *
     * @OA\Get(
     *       path="/{tenant}/activity-groups",
     *       summary="Get Activity Groups",
     *       operationId="getActivityGroups",
     *       tags={"ActivityGroup"},
     *       security = {{"BearerAuth": {}}},
     *       description="Authenticate using a bearer token",
     *       @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=500,description="Internal server error")
     *  )
     *
     * @param Request $request
     * @param integer $id
     * @return Response
     * @throws Exception
     */
    public function get(Request $request, int $id = null) : Response
    {
        try{
            $query = isset($id) ? ActivityGroup::where('id', $id) : ActivityGroup::query();

            if ($request->has('with') && ($request->input('with') != '')) {
                $query = $query->with($request->with);
            }

            $data = isset($id) ? $query->first() : $query->get();

            if((isset($id) && !isset($data)) || (!isset($id) && count($data) == 0)){
                return $this->success([], 'No activity group record(s) found', [], Response::HTTP_NOT_FOUND);
            }

            return $this->success($data, 'Activity groups successfully retrieved', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve activity groups.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
