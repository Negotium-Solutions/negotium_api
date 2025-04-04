<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Requests\Tenant\ProcessExecutionRequest;
use App\Models\Tenant\DynamicModel;
use App\Models\Tenant\DynamicModelFieldGroup;
use App\Models\Tenant\ProcessStatus;
use App\Models\Tenant\ProfileProcess;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
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
            $process = DynamicModel::find($id);
            $data = $process->getRecord($request, $process->id);

            return $this->success($data, 'processes successfully retrieved', [], Response::HTTP_OK);
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve process.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(ProcessExecutionRequest $request) : Response
    {
        try {
            $dynamicModel = DynamicModel::find($request->input('process_id'));

            $old_value = $dynamicModel;
            $new_value = $request->all();

            if ((!isset($dynamicModel))) {
                return $this->success([], 'No dynamic model record found to update', [], Response::HTTP_NO_CONTENT);
            }

            foreach ($request->input('fields') as $field) {
                if (array_key_exists($field['field'], $dynamicModel->getAttributes())) {
                    if (!in_array($field['field'], ['id', 'created_at', 'updated_at', 'deleted_at', 'parent_id'])) {
                        switch( $field['dynamic_model_field_type_id']) {
                            case 16:
                                $jsonData = json_encode($field['value']);

                                $file = json_decode($jsonData);
                                if (isset($file->base64)) {
                                    $fileContent = base64_decode($file->base64);
                                    $parts = explode('.', $file->name);
                                    $extension = end($parts);
                                    $fileName = 'file_'.date('Y_m_d_h_i_s').'.'.$extension;
                                    $filePath = 'uploads/process-execution/'.date('Y-m-d').'/' . $fileName;
                                    Storage::put($filePath, $fileContent);
                                    $dynamicModel->{$field['field']} = $filePath;
                                }
                            break;
                            default:
                                $dynamicModel->{$field['field']} = $field['value'];
                            break;
                        }
                    }
                }
            }
            $dynamicModel->updated_at = now();

            if ($dynamicModel->save() === false) {
                throw new \RuntimeException('Could not update profile dynamic model');
            }

            $steps = DynamicModelFieldGroup::where('schema_id', $request->input('schema_id'))
                    ->orderBy('order')
                    ->get();

            $step = null;
            foreach ($steps as $key => $_step) {
                if ($_step->id === $request->get('id')) {
                    if (($key + 1) === $steps->count()) {
                        $step = $steps[$key];
                    } else {
                        $step = $steps[$key + 1];
                    }
                }
            }

            $profileProcess = ProfileProcess::find($dynamicModel->parent_id);
            $currentStep = DynamicModelFieldGroup::find($profileProcess->step_id);

            if($step->order > $currentStep->order) {
                $profileProcess->step_id = $step->id;
            }
            $profileProcess->process_status_id = ProcessStatus::ACTIVE;
            $profileProcess->save();

        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the profile', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success(['step_id' => $step->id], 'profile successfully updated', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }

    public function getCurrentProcessStep($process_id, $profile_id)
    {
        try {
            $profileProcess = ProfileProcess::with(['step'])->where('process_id', $process_id)->where('profile_id', $profile_id)->first();

            return $this->success($profileProcess, 'current processes step successfully retrieved.', [], Response::HTTP_CREATED, [], []);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve current processes step.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
