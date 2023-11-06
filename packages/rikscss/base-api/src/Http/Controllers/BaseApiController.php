<?php

namespace Rikscss\BaseApi\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Route;
use Rikscss\BaseApi\Models\BaseApi;

class BaseApiController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    protected $logging = true;
    protected $logPayload = true;
    protected $logResponse = true;

    protected $debug = false;

    public function __construct()
    {
        // $this->middleware(['auth:api', 'auth:sanctum']);
        $this->debug = env('APP_DEBUG');
    }

    public function logSuccess($message, $user_id, $route, $payload, $response, $code = 200, $old_value = [], $new_value = [])
    {
        if ($this->logging == true) {
            $flowLog = new BaseApi();
            $flowLog->user_id = $user_id;
            $flowLog->route = $route;
            $flowLog->payload = json_encode($payload);
            $flowLog->response = json_encode($response);
            $flowLog->old_value = json_encode($old_value);
            $flowLog->new_value = json_encode($new_value);
            $flowLog->message = $message;
            $flowLog->code = $code;
            $flowLog->is_error = 'success';
            $flowLog->save();
        }

        return isset($flowLog->id) ? $flowLog->id : null;
    }

    public function logError($message, $user_id, $route, $payload, $response, $code, $old_value = [], $new_value = [])
    {
        if ($this->logging == true) {
            $flowLog = new BaseApi();
            $flowLog->user_id = $user_id;
            $flowLog->route = $route;
            $flowLog->payload = json_encode($payload);
            $flowLog->response = json_encode($response);
            $flowLog->old_value = json_encode($old_value);
            $flowLog->new_value = json_encode($new_value);
            $flowLog->message = $message;
            $flowLog->code = $code;
            $flowLog->is_error = 'error';
            $flowLog->save();
        }

        return isset($flowLog->id) ? $flowLog->id : null;
    }

    public function success($data, $message = 'flow API call successful', $payload = null, $code = 200, $old_value = [], $new_value = [])
    {
        $response = [
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];

        if ($this->logging == true) {
            $logId = $this->logSuccess(
                $message,
                1, // auth('api')->user()->id(), // Todo: Login the correct way
                Route::currentRouteName(),
                $this->logPayload == true ? $payload : null,
                $this->logResponse == true ? $response : null,
                $code,
                $old_value,
                $new_value
            );
        }

        if ($this->debug) {
            $response['payload'] = $payload;
            $response['old_value'] = $old_value;
            $response['new_value'] = $new_value;
            $response['log_id'] = $logId;
        }

        return response($response)->setStatusCode($code);
    }

    public function error($data, $message = "An error occured while trying to make an API call", $payload = null, $code = 500, $old_value = [], $new_value = [])
    {
        $response = [
            'code' => $code,
            'message' => $message,
            'errors' => $data
        ];

        if ($this->logging == true) {
            $logId = $this->logError(
                $message,
                1, // auth('api')->user()->id, // Todo: Login the correct way
                Route::currentRouteName(),
                $this->logPayload == true ? $payload : null,
                $this->logResponse == true ? $response : null,
                $code,
                $old_value,
                $new_value
            );
        }

        if ($this->debug) {
            $response['payload'] = $payload;
            $response['old_value'] = $old_value;
            $response['new_value'] = $new_value;
            $response['log_id'] = $logId;
        }

        return response($response)->setStatusCode($code);
    }
}
