<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Process;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

class ProcessController extends BaseAPIController
{
    /**
     * Get process(s) resource(s).
     */
    public function get(Request $request, $id = null) : Response
    {
        try{
            $query = isset($id) ? Process::find($id) : Process::query();
            
            $data = isset($id) ? $query : $query->get();
            
            if((isset($id) && !isset($data)) || (!isset($id) && count($data) == 0)){
                return $this->success([], 'No process record(s) found', [], Response::HTTP_NOT_FOUND);
            }

            return $this->success($data, 'processes successfully retrieved', [], Response::HTTP_OK);
        }catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve process.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created process
     */
    public function create(Request $request) : Response
    {
        $validator = \Validator::make($request->all(),
            ['name' => 'string|required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(),  Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $process = new Process();
            $process->name = $request->name;

            if ($process->save() === false) {
                throw new \RuntimeException('Could not save process');
            }

            return $this->success(['id' => $process->id], 'process successfully created.', $request->all(),  Response::HTTP_CREATED);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create process.', [],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the the process
     */
    public function update(Request $request, $id) : Response
    {
        $validator = \Validator::make($request->all(),
            ['name' => 'string|required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $process = Process::find($id);
            if((!isset($process))){
                return $this->success([], 'No process record found to update', [], Response::HTTP_NOT_FOUND);
            }

            $old_value = Process::findOrFail($id);
            $new_value = $request->all();

            if ($process->updateOrFail($request->all()) === false) {
                throw new \RuntimeException('Could not update the process');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the process', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success([], 'process successfully updated', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }

    /**
     * Delete the process
     */
    public function delete($id) : Response
    {
        try {
            $process = Process::find($id);
            if((!isset($process))){
                return $this->success([], 'No process record found to delete', [], Response::HTTP_NOT_FOUND);
            }

            if ($process->delete() === false) {
                throw new \RuntimeException('Could not delete the process');
            }

            return response()->noContent();
        } catch (\Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the the process', ['id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
