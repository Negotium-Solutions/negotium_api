<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use \App\Models\Domain;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
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
        $user->last_login_ip = isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR']:'127.0.0.1';
        $user->save();

        $token = $user->createToken('API_TOKEN')->plainTextToken;

        activity('User')->performedOn($user)->causedBy($request->user())->log('User '.$user->email.' logged in successfully');

        return response([
            'code' => SymfonyResponse::HTTP_OK,
            'message' => 'user logged in successfully',
            'data' => [
                'token' => $token,
                'user' => [
                    "first_name" => $user->first_name,
                    "last_name" => $user->last_name,
                    "email" => $user->email
                ]
            ]
        ])->setStatusCode(SymfonyResponse::HTTP_OK);
    }

    /**
     * User login
     * @param Request $request
     * @return User
     */
    public function getTenantAndLogin(Request $request) : Response
    {
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

            // Get tenant
            $domain_name = explode('@', $request->email)[1];
            $domain = Domain::where('domain', $domain_name)->first();

            if (!isset($domain->tenant_id)) {
                return response([
                    'code' => SymfonyResponse::HTTP_NOT_FOUND,
                    'message' => 'Domain for email provided not found',
                ])->setStatusCode(SymfonyResponse::HTTP_NOT_FOUND);
            }

            $request = Request::create('/api/'.$domain->tenant_id.'/auth/login', 'POST');
            $response = Route::dispatch($request);

        } catch (Throwable $exception) {
            return response([
                'code' => SymfonyResponse::HTTP_BAD_REQUEST,
                'message' => 'There was an error trying to update the user',
                'errors' => $exception->getMessage()
            ])->setStatusCode(SymfonyResponse::HTTP_BAD_REQUEST);
        }

        $response_data = json_decode($response->getContent(), true);
        if (isset($response_data['code']) && $response_data['code'] === 200) {
             $response_data["data"]['tenant'] = $domain->tenant_id;
        }

        return response($response_data);
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
            'code' => SymfonyResponse::HTTP_OK,
            'message' => 'user logged out successfully',
            'data' => []
        ])->setStatusCode(SymfonyResponse::HTTP_OK);
    }
}
