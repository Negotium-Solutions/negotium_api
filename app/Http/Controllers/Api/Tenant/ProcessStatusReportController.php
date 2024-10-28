<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Models\Tenant\DynamicModel;
use App\Models\Tenant\DynamicModelType;
use App\Models\Tenant\ProfileProcess;
use App\Models\Tenant\Schema as TenantSchema;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

class ProcessStatusReportController extends BaseApiController
{
    /**
     * Get process execution
     *
     * @OA\Get(
     *       path="/{tenant}/process-execution",
     *       summary="Get Process Execution",
     *       operationId="getProcessExectution",
     *       tags={"ProcessExecution"},
     *       security = {{"BearerAuth": {}}},
     *       description="Authenticate using a bearer token",
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
        try {
            $profileTypes = TenantSchema::where('dynamic_model_type_id', DynamicModelType::PROFILE)->get();
            foreach ($profileTypes as $key => $profileType) {
                // Session::put('table_name', $profileType->table_name);
                Session::put('schema_id', $profileType->id);
                $profiles = DynamicModel::where('schema_id', $profileType->id)->get();
                $profileTypes[$key]['profiles'] = $profiles;
                $profileTypes[$key]['profiles_count'] = $profiles->count();

                foreach ($profiles as $profileKey => $profile) {
                    $processIds = ProfileProcess::where('profile_id', $profile->id)->pluck('process_id')->toArray();
                    $processes = TenantSchema::whereIn('id', $processIds)->get();
                    $processes_start_rate_percentage = 0;
                    if ($processes->count() > 0) {
                        $processes_start_rate_percentage = ProfileProcess::where('profile_id', $profile->id)->where('process_status_id', '>', 1)->count() / $processes->count() * 100;
                    }
                    $profiles[$profileKey]['processes'] = $processes;
                    $profiles[$profileKey]['processes_count'] = $processes->count();
                    $profiles[$profileKey]['processes_start_rate_percentage'] = $processes_start_rate_percentage;
                }
            }

            return $this->success($profileTypes, 'report successfully retrieved', [], Response::HTTP_OK);
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve reports.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
