<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Models\Tenant\Process;
use App\Models\Tenant\ProfileProcess;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

class ProcessExecutionController extends BaseApiController
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
        $validator = Validator::make($request->all(),
            ['profile_id' => 'string|required'],
            ['process_id' => 'string|required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $profileProcess = $this->getProfileProcess($request->input('profile_id'), $request->input('process_id'));

            $process = Process::find($request->input('process_id'));
            $steps = $process->dynamicModel($profileProcess->id)->propertiesByStep($process->id);

            $process['parent_id'] = $profileProcess->id;
            $process['step_id'] = isset($steps[0]->id) ? $steps[0]->id : 0;
            $process['validate'] = $request->has('validate') ? $request->input('validate') : 1;
            $process['steps'] = $process->dynamicModel($profileProcess->id)->propertiesByStep($process->id);

            return $this->success($process, 'processes successfully retrieved', [], Response::HTTP_OK);
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve process.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getProfileProcess($profile_id, $process_id) : ProfileProcess
    {
        $processProfile = ProfileProcess::where('profile_id', $profile_id)
            ->where('process_id', $process_id)
            ->first();

        if ( empty($processProfile) ) {
            $processProfile = new ProfileProcess();
            $processProfile->profile_id = $profile_id;
            $processProfile->process_id = $process_id;
            $processProfile->save();
        }

        return $processProfile;
    }
}
