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
     * User login
     * @param Request $request
     * @return User
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
        $user->last_login_ip = $_SERVER['REMOTE_ADDR'];
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
     * @return bool
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
