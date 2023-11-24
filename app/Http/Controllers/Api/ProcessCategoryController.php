<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProcessCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

class ProcessCategoryController extends BaseAPIController
{
    /**
     * Get process category(s) resource(s).
     */
    public function get(Request $request, $id = null) : Response
    {
        $query = isset($id) ? ProcessCategory::find($id) : ProcessCategory::query();

        $data = isset($id) ? $query : $query->get();

        return $this->success($data, 'process categories successfully retrieved', [], Response::HTTP_OK);
    }

    /**
     * Store a newly created process category
     */
    public function create(Request $request) : Response
    {
        $validator = \Validator::make($request->all(),
            ['name' => 'string|required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), 422);
        }

        try {
            $user = new User();
            $user->name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->email_verified_at = now();
            $user->password = $request->password;
            $user->avatar = $request->avatar;

            if ($user->save() === false) {
                throw new \RuntimeException('Could not save user');
            }

            return $this->success(['id' => $user->id], 'user successfully created.', $request->all(), 200);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create user.', []);
        }
    }

    /**
     * Update the the process category
     */
    public function update(Request $request, $id) : Response
    {
        $validator = \Validator::make($request->all(),
            ['name' => 'string|required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), 422);
        }

        try {
            $user = User::findOrFail($id);
            $old_value = User::findOrFail($id);
            $new_value = $request->all();

            if ($user->updateOrFail($request->all()) === false) {
                throw new \RuntimeException('Could not update the process category');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the process category', $request->all(), Response::HTTP_BAD_REQUEST);
        }

        return $this->success([], 'process category successfully updated', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }

    /**
     * Delete the process category
     */
    public function delete($id) : Response
    {
        try {
            $user = ProcessCategory::find($id);

            if ($user->delete() === false) {
                throw new \RuntimeException('Could not delete the process category');
            }

            return $this->success([], 'process category successfully deleted', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the the process category', ['id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
