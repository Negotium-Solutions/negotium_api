<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use \Symfony\Component\HttpFoundation\Response as SymphonyResponse;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): Response
    {
        $response = [
            'code' => SymphonyResponse::HTTP_UNAUTHORIZED,
            'message' => 'Unauthorised access',
            'data' => []
        ];

        return response($response)->setStatusCode(SymphonyResponse::HTTP_UNAUTHORIZED);
    }
}
