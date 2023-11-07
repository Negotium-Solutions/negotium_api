<?php

namespace Rikscss\BaseApi\Http\Controllers\Api;

use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;
use Illuminate\Http\Request;
use Rikscss\BaseApi\Models\BaseApiLog;

class BaseApiLogController extends BaseAPIController
{
    /**
     * Get BaseApi(s) resource(s).
     */
    public function get(Request $request, $id = null) : Response
    {
        $query = isset($id) ? BaseApiLog::find($id) : BaseApiLog::query();

        $data = isset($id) ? $query : $query->get();

        return $this->success($data, 'api logs successfully retrieved', [], Response::HTTP_OK);
    }

    /**
     * Store a newly created BaseApi.
     */
    public function create(Request $request) : Response
    {
        $validator = \Validator::make($request->all(),
            ['route' => 'string|required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), 422);
        }

        try {
            $baseApiLog = new BaseApiLog();
            $baseApiLog->user_id = $request->user_id;
            $baseApiLog->route = $request->route;
            $baseApiLog->payload = json_encode($request->payload);
            $baseApiLog->response = json_encode($request->response);
            $baseApiLog->old_value = json_encode($request->old_value);
            $baseApiLog->new_value = json_encode($request->new_value);
            $baseApiLog->message = $request->message;
            $baseApiLog->code = $request->code;
            $baseApiLog->is_error = $request->is_error;
            $baseApiLog->save();

            if ($baseApiLog->save() === false) {
                throw new \RuntimeException('Could not save base api log');
            }

            return $this->success(['id' => $baseApiLog->id], 'base api log successfully created.', $request->all(), 200);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create the base api log.', []);
        }
    }

    /**
     * Update the BaseApi.
     */
    public function update(Request $request, $id) : Response
    {
        $validator = \Validator::make($request->all(),
            ['message' => 'string|required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), 422);
        }

        try {
            $baseApiLog = BaseApiLog::findOrFail($id);

            if ($baseApiLog->updateOrFail($request->all()) === false) {
                throw new \RuntimeException('Could not update BaseApi');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the base api log', $request->all(), Response::HTTP_BAD_REQUEST);
        }

        return $this->success([], 'base api log successfully updated', $request->all(), Response::HTTP_OK);
    }

    /**
     * Delete the BaseApi.
     */
    public function delete($id) : Response
    {
        try {
            $baseApiLog = BaseApiLog::find($id);

            if ($baseApiLog->delete() === false) {
                throw new \RuntimeException('Could not delete the base api log');
            }

            return $this->success([], 'base api log successfully deleted', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the base api log', ['id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
