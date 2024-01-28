<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AuthController extends Controller
{
    /**
     * @OA\POST(
     *      path="/auth/login",
     *      operationId="loginUser",
     *      
     *      summary="Logs in the user",
     *      tags={"user"},
     *      description="Logs in the user by email and password",
     *      @OA\Parameter(
     *          name="email",
     *          description="The user email address or username",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *       @OA\Parameter(
     *          name="password",
     *          description="The user password",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
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
    public function login(Request $request) : Response
    {
        $user = User::where('email', $request->email)->first();

        try {
            $validator = \Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if($validator->fails()){
                activity('User')->causedBy($request->user())->log('Input validation error for user '.$request->email);

                return response([
                    'code' => SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => 'Input validation error',
                    'errors' => $validator->errors()
                ])->setStatusCode(SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY);
            }

            if(!Auth::attempt($request->only(['email', 'password']))) {
                activity('User')->causedBy($request->user())->log('Wrong email or password provided for user '.$request->email);

                return response([
                    'code' => SymfonyResponse::HTTP_UNAUTHORIZED,
                    'message' => 'Wrong email or password provided',
                    'errors' => []
                ])->setStatusCode(SymfonyResponse::HTTP_UNAUTHORIZED);
            }

        } catch (Throwable $exception) {
            activity('User')->performedOn($user)->causedBy($request->user())->log('There was an error trying to update the user '.$user->email);

            return response([
                'code' => SymfonyResponse::HTTP_BAD_REQUEST,
                'message' => 'There was an error trying to update the user',
                'errors' => $exception->getMessage()
            ])->setStatusCode(SymfonyResponse::HTTP_BAD_REQUEST);
        }

        $user->last_login_at = now();
        $user->last_login_ip = isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR']:'127.0.0.1';
        $user->save();

        $token = $user->createToken('API_TOKEN')->plainTextToken;

        activity('User')->performedOn($user)->causedBy($request->user())->log('User '.$user->email.' logged in successfully');

        return response([
            'code' => SymfonyResponse::HTTP_OK,
            'message' => 'user logged in successfully',
            'data' => [
                'token' => $token
            ]
        ])->setStatusCode(SymfonyResponse::HTTP_OK);
    }

    /**
     * @OA\POST(
     *      path="/auth/logout",
     *      operationId="logoutUser",
     *      summary="Logs out the user",
     *      tags={"user"},
     *      security = {{"BearerAuth": {}}},
     *      description="Log out the user using the Bearer<Token>",
     *      @OA\Parameter(
     *          name="Authorization",
     *          description="Bearer Token",
     *          required=true,
     *          in="header",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
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
    public function logout(Request $request): Response
    {
        try {
            if(auth()->user()->tokens()->delete() === false){
                throw new \RuntimeException('user could not be logged out');
            }
        } catch (Throwable $exception) {
            activity('User')->performedOn(auth()->user())->causedBy($request->user())->log('User '.auth()->user()->email.' could not be logged out');

            return response([
                'code' => SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'user could not be logged out',
                'errors' => [
                    $exception->getMessage()
                ]
            ])->setStatusCode(SymfonyResponse::HTTP_OK);
        }

        activity('User')->performedOn(auth()->user())->causedBy($request->user())->log('User '.auth()->user()->email.' logged out successfully');

        return response([
            'message' => 'user logged out successfully',
            'data' => []
        ])->setStatusCode(SymfonyResponse::HTTP_OK);
    }
}
