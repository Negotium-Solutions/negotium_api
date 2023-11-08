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
        try {
            $validator = \Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if($validator->fails()){
                return response([
                    'code' => SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => 'Input validation error',
                    'errors' => $validator->errors()
                ])->setStatusCode(SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY);
            }

            if(!Auth::attempt($request->only(['email', 'password']))) {
                return response([
                    'code' => SymfonyResponse::HTTP_UNAUTHORIZED,
                    'message' => 'Wrong email or password provided',
                    'errors' => []
                ])->setStatusCode(SymfonyResponse::HTTP_UNAUTHORIZED);
            }

        } catch (Throwable $exception) {
            return response([
                'code' => SymfonyResponse::HTTP_BAD_REQUEST,
                'message' => 'There was an error trying to update the user',
                'errors' => $exception->getMessage()
            ])->setStatusCode(SymfonyResponse::HTTP_BAD_REQUEST);
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('API_TOKEN')->plainTextToken;

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
    public function logout(Request $request): bool
    {
        auth()->user()->tokens()->delete();

        return response([
            'message' => 'user logged out successfully',
            'data' => []
        ])->setStatusCode(SymfonyResponse::HTTP_OK);
    }
}
