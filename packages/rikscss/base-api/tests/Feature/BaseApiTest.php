<?php

namespace Rikscss\BaseApi\Tests\Feature;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Rikscss\BaseApi\Http\Controllers\Api\BaseApiLogController;
use Rikscss\BaseApi\Models\BaseApiLog;
use Rikscss\BaseApi\Tests\TestCase;

class BaseApiTest extends TestCase
{
    public function testCanGetBaseApiLog() : void
    {
        BaseApiLog::factory()->count(12)->create();
        $base_api_log = BaseApiLog::orderBy('created_at', 'desc')->first();

        $response = $this->get('/api/base-api-log/'.$base_api_log->id);

        $this->assertTrue(isset($response['data']['payload']));

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'api logs successfully retrieved',
            'data' => []
        ]);
    }

    public function testCanGetBaseApiLogs() : void
    {
        BaseApiLog::factory()->count(3)->create();
        BaseApiLog::factory([
            'message' => 'api log successfully created',
            'code' => Response::HTTP_OK,
            'is_error' => 'success'
        ])->create();

        $response = $this->get('/api/base-api-log');

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'api logs successfully retrieved',
            'data' => [
                [],
                [],
                [],
                []
            ]
        ]);
    }

    public function testGetUserNotFound() : void
    {
        $response = $this->get('/api/base-api-log/9a8ea593-6393-4677-ae6a-6923e008876b');

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'api logs successfully retrieved',
            'data' => null
        ]);
    }

    public function testCanUpdateBaseApiLog() : void
    {
        // Create a base api log
        $base_api_log = BaseApiLog::factory([
            'message' => 'api log successfully created',
            'code' => Response::HTTP_OK,
            'is_error' => 'success'
        ])->create();

        $response = $this->get('/api/base-api-log/'.$base_api_log->id);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'api logs successfully retrieved',
            'data' => []
        ]);

        // Update a base api log
        $response = $this->put('/api/base-api-log/update/'.$base_api_log->id, [
            'message' => 'api log successfully created updated'
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'base api log successfully updated',
            'data' => []
        ]);

        $response = $this->get('/api/base-api-log/'.$base_api_log->id);
        // After updated
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'api logs successfully retrieved',
            'data' => [
                'message' => 'api log successfully created updated'
            ]
        ]);
    }

    public function testCanNotUpdateBaseApilog() : void
    {
        $base_api_log = BaseApiLog::factory()->create();

        $response = $this->put('/api/base-api-log/update/'.$base_api_log->id);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'message' => 'Input validation error',
            'errors' => []
        ]);
    }

    public function testCanCreateBaseApiLog() : void
    {
        $http_response = [
            ['code' => Response::HTTP_OK, 'is_error' => 'success', 'message' => 'Request successfully completed'],
            ['code' => Response::HTTP_UNPROCESSABLE_ENTITY, 'is_error' => 'error', 'message' => 'Invalid input provided'],
            ['code' => Response::HTTP_UNAUTHORIZED, 'is_error' => 'error', 'message' => 'Unauthorized content'],
            ['code' => Response::HTTP_INTERNAL_SERVER_ERROR, 'is_error' => 'error', 'message' => 'Server error occured']
        ];

        $index = rand(0, 3);
        $old_value = [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail()
        ];
        $new_value = [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail()
        ];

        $response = $this->post('/api/base-api-log/create', [
            'user_id' => Str::uuid(),
            'route' => fake()->unique()->safeEmail(),
            'payload' => json_encode($new_value),
            'response' => json_encode(['message' => $http_response[$index]['message'], 'data' => ['user_id' => Str::uuid()]]),
            'old_value' => json_encode($old_value),
            'new_value' => json_encode($new_value),
            'message' => $http_response[$index]['message'],
            'code' => $http_response[$index]['code'],
            'is_error' => $http_response[$index]['is_error']
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'base api log successfully created.',
            'data' => [
                "id" => $response['data']['id']
            ]
        ]);
    }

    public function testCanDeleteBaseApiLog() : void
    {
        $base_api_log = BaseApiLog::factory()->create();

        $response = $this->delete('/api/base-api-log/delete/'.$base_api_log->id);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'base api log successfully deleted',
            'data' => []
        ]);
    }
}
