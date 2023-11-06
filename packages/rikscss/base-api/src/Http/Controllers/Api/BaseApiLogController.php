<?php

namespace Rikscss\BaseApi\Http\Controllers\Api;

use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;
use Illuminate\Http\Request;
use Rikscss\BaseApiController\Models\BaseApi;

class BaseApiLogController extends BaseAPIController
{
    /**
     * Get BaseApi(s) resource(s).
     */
    public function get(Request $request, $id = null) : Response
    {
        $query = isset($id) ? BaseApi::find($id) : BaseApi::query();

        $data = isset($id) ? $query : $query->get();

        return $this->success($data, 'BaseApis successfully retrieved', [], Response::HTTP_OK);
    }

    /**
     * Store a newly created BaseApi.
     */
    public function create(Request $request) : Response
    {
        $validator = \Validator::make($request->all(),
            ['first_name' => 'string|required'],
            ['last_name' => 'string|required'],
            ['email' => 'unique:email|email|required'],
            ['password' => 'required|required|confirmed']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), 422);
        }

        try {
            $BaseApi = new BaseApi();
            $BaseApi->first_name = $request->first_name;
            $BaseApi->last_name = $request->last_name;
            $BaseApi->email = $request->email;
            $BaseApi->email_verified_at = now();
            $BaseApi->password = $request->password;
            $BaseApi->avatar = $request->avatar;

            if ($BaseApi->save() === false) {
                throw new \RuntimeException('Could not save BaseApi');
            }

            return $this->success(['id' => $BaseApi->id], 'BaseApi successfully created.', $request->all(), 200);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create BaseApi.', []);
        }
    }

    /**
     * Update the BaseApi.
     */
    public function update(Request $request, $id) : Response
    {
        $validator = \Validator::make($request->all(),
            ['first_name' => 'string|required'],
            ['last_name' => 'string|required'],
            ['email' => 'unique|email|required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), 422);
        }

        try {
            $BaseApi = BaseApi::findOrFail($id);

            if ($BaseApi->updateOrFail($request->all()) === false) {
                throw new \RuntimeException('Could not update BaseApi');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the BaseApi', $request->all(), Response::HTTP_BAD_REQUEST);
        }

        return $this->success([], 'BaseApi successfully updated', $request->all(), Response::HTTP_OK);
    }

    /**
     * Delete the BaseApi.
     */
    public function destroy($id) : Response
    {
        try {
            $BaseApi = BaseApi::find($id);

            if ($BaseApi->delete() === false) {
                throw new \RuntimeException('Could not delete the BaseApi');
            }

            return $this->success([], 'BaseApi successfully deleted', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the BaseApi', ['BaseApi_id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
