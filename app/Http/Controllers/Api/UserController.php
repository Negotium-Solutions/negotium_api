<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends BaseAPIController
{
    /**
     * Get user(s) resource(s).
     */
    public function get(Request $request, $id = null) : Response
    {
        $query = isset($id) ? User::find($id) : User::query();

        $data = isset($id) ? $query : $query->get();

        return $this->success($data, 'users successfully retrieved', [], Response::HTTP_OK);
    }

    /**
     * Store a newly created user.
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
            $user = new User();
            $user->first_name = $request->first_name;
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
     * Update the user.
     */
    public function update(Request $request, $id) : Response
    {
        $validator = \Validator::make($request->all(),
            ['email' => 'email']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), 422);
        }

        try {
            $user = User::findOrFail($id);
            $old_value = User::findOrFail($id);
            $new_value = $request->all();

            if ($user->updateOrFail($request->all()) === false) {
                throw new \RuntimeException('Could not update user');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the user', $request->all(), Response::HTTP_BAD_REQUEST);
        }

        return $this->success([], 'user successfully updated', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }

    /**
     * Delete the user.
     */
    public function delete($id) : Response
    {
        try {
            $user = User::find($id);

            if ($user->delete() === false) {
                throw new \RuntimeException('Could not delete the user');
            }

            return $this->success([], 'user successfully deleted', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the user', ['user_id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
