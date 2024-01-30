<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends BaseAPIController
{
    /**
         * 
         * 
         * @OA\GET(
         *      path="/user/{id}",
         *      summary="Get a user",
         *      operationId="getUser",
         *      tags={"user"},
         *      security = {{"BearerAuth": {}}},
         *      description="This can only be done by the logged in user.",
         *      @OA\Parameter(
         *          name="id",
         *          description="User Id",
         *          required=true,
         *          in="path",
         *          @OA\Schema(
         *              type="string"
         *          )
         *      ),
         *          
         *      @OA\Response(
         *          response=200,
         *          description="Successful operation",
         *          @OA\JsonContent()
         *       ),
         *      @OA\Response(
         *          response=401,
         *          description="Unauthenticated",
         *      ),
         * ),
         * @OA\GET(
         *      path="/user",
         *      summary="Get users",
         *      operationId="getUsers",
         *      tags={"user"},
         *      security = {{"BearerAuth": {}}},
         *      description="This can only be done by the logged in user.",
         *      @OA\Response(
         *          response=200,
         *          description="Successful operation",
         *          @OA\JsonContent()
         *       ),
         *      @OA\Response(
         *          response=401,
         *          description="Unauthenticated",
         *      )
         * )
         * 
    */

    
    public function get(Request $request, $id = null) : Response
    {
        $query = isset($id) ? User::find($id) : User::query();

        $data = isset($id) ? $query : $query->get();
return false;
        return $this->success($data, 'users successfully retrieved', [], Response::HTTP_OK);
    }

    /**
     * 
     * 
     * @OA\POST(
     *      path="/user/create",
     *      operationId="createUser",
     *      summary="create a new user",
     *      tags={"user"},
     *      security = {{"BearerAuth": {}}},
     *      description="This can only be done by the logged in user.",
     *      @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="first_name",
     *                   type="string",
     *              ),
     *               @OA\Property(
     *                   property="last_name",
     *                   type="string",
     *              ),
     *               @OA\Property(
     *                   property="email",
     *                   type="string",
     *              ),
     *               @OA\Property(
     *                   property="password",
     *                   type="string",
     *              ),
     *               @OA\Property(
     *                   property="avatar",
     *                   type="string",
     *              ),
     *               @OA\Property(
     *                   property="last_login_at",
     *                   type="string",
     *                   format="date-time"
     *              ),
     *               @OA\Property(
     *                   property="last_login_ip",
     *                   type="string"
     *              ),
     *           )
     *       )
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
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
     * 
     * 
     * @OA\PUT(
     *      path="/user/update/{id}",
     *      operationId="updateUser",
     *      summary="Update user",
     *      tags={"user"},
     *      security = {{"BearerAuth": {}}},
     *      description="This can only be done by the logged in user.",
     *      @OA\Parameter(
     *          name="id",
     *          description="The user's id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="first_name",
     *                   type="string",
     *              ),
     *               @OA\Property(
     *                   property="last_name",
     *                   type="string",
     *              ),
     *               @OA\Property(
     *                   property="email",
     *                   type="string",
     *              ),
     *               @OA\Property(
     *                   property="password",
     *                   type="string",
     *              ),
     *               @OA\Property(
     *                   property="avatar",
     *                   type="string",
     *              ),
     *               @OA\Property(
     *                   property="last_login_at",
     *                   type="string",
     *                   format="date-time"
     *              ),
     *               @OA\Property(
     *                   property="last_login_ip",
     *                   type="string"
     *              ),
     *           )
     *       )
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
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
     * 
     * 
     * @OA\DELETE(
     *      path="/user/delete/{id}",
     *      summary="Delete a user",
     *      operationId="deleteUser",
     *      tags={"user"},
     *      security = {{"BearerAuth": {}}},
     *      description="This can only be done by the logged in user.",
     *      @OA\Parameter(
     *          name="id",
     *          description="The user's id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *          
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     * )
     * 
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
