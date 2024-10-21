<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Requests\Tenant\DynamicModelFieldRequest;
use App\Http\Requests\Tenant\ProcessExecutionRequest;
use App\Models\Tenant\DynamicModel;
use App\Models\Tenant\ProfileProcess;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
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
    public function get(Request $request, $id = null) : Response
    {
        $validator = Validator::make($request->all(),
            ['profile_id' => 'string|required'],
            ['process_id' => 'string|required'],
            ['process_schema_id' => 'string|required'],
            ['profile_schema_id' => 'string|required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $this->getProfileProcess($request->input('process_schema_id'), $request->input('process_id'));

            if ( (int)$id === 0) {
                $process = new DynamicModel();
                $process->save();
            } else {
                $process = DynamicModel::find($id);
            }

            $data = $process->getRecord($request, $process->id);

            return $this->success($data, 'processes successfully retrieved', [], Response::HTTP_OK);
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve process.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getProfileProcess($profile_id, $process_id) : void
    {
        $processProfile = ProfileProcess::where('profile_id', $profile_id)
            ->where('process_id', $process_id)
            ->first();

        if ( empty($processProfile) ) {
            $processProfile = new ProfileProcess();
            $processProfile->profile_id = $profile_id;
            $processProfile->process_id = $process_id;
            $processProfile->process_status_id = 1;
            $processProfile->save();
        }
    }

    public function update(ProcessExecutionRequest $request) : Response
    {
        try {
            Session::put('table_name', $request->input('schema_table'));
            $dynamicModel = new DynamicModel();
            $dynamicModel = $dynamicModel->where('parent_id', $request->input('parent_id'))->first();

            $old_value = $dynamicModel;
            $new_value = $request->all();

            if ((!isset($dynamicModel))) {
                return $this->success([], 'No dynamic model record found to update', [], Response::HTTP_NO_CONTENT);
            }

            /*
            $fields = null;
            if ($request->has('step_id') && $request->input('step_id') > 0) {
                $steps = $request->input('steps');
                foreach ($steps as $step) {
                    if ($step['id'] === $request->input('step_id')) {
                        $fields = $step['fields'];
                    }
                }
            }

            if ($fields === null) {
                $fields = $request->all();
            }
            */

            foreach ($request->all() as $key => $value) {
                // foreach ($fields as $key => $value) {
                if (array_key_exists($key, $dynamicModel->getAttributes())) {
                    if (!in_array($key, ['id', 'created_at', 'updated_at', 'deleted_at', 'parent_id'])) {
                        $dynamicModel->{$key} = $value;
                    }
                }
            }
            $dynamicModel->updated_at = now();

            if ($dynamicModel->save() === false) {
                throw new \RuntimeException('Could not update profile dynamic model');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the profile', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success(['id' => $dynamicModel->id], 'profile successfully updated', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }
}
