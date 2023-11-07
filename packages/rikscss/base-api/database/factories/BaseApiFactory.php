<?php

namespace Rikscss\BaseApi\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Rikscss\BaseApi\Models\BaseApiLog;
use Symfony\Component\HttpFoundation\Response;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class BaseApiFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = BaseApiLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
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

        return [
            'user_id' => Str::uuid(),
            'route' => fake()->unique()->safeEmail(),
            'payload' => json_encode($new_value),
            'response' => json_encode(['message' => $http_response[$index]['message'], 'data' => ['user_id' => Str::uuid()]]),
            'old_value' => json_encode($old_value),
            'new_value' => json_encode($new_value),
            'message' => $http_response[$index]['message'],
            'code' => $http_response[$index]['code'],
            'is_error' => $http_response[$index]['is_error']
        ];
    }
}
