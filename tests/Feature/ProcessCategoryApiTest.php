<?php

namespace Feature;

use App\Models\ProcessCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProcessCategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function testCanGetAProcessCategory() : void
    {
        $token = $this->getToken();

        $process_category = ProcessCategory::factory(['name' => 'HR Processes'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->get('/api/process-category/'.$process_category->id);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'process categories successfully retrieved',
            'data' => ['name' => 'HR Processes']
        ]);
    }

    public function testCanGetUsers() : void
    {
        $token = $this->getToken();

        User::factory()->count(6)->create();

        User::factory()->create([
            'first_name' => 'Sakhile',
            'last_name' => 'Van Heerden'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->get('/api/user');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertTrue(count($response['data']) === 8); // Number of users in the database, plus 1 created by getToken
    }

    public function testGetUserNotFound() : void
    {
        $token = $this->getToken();

        User::factory()->create([
            'first_name' => 'Sakhile',
            'last_name' => 'Van Heerden'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->get('/api/user/9a90bb05-4a72-4b82-8e60-2b069a15d34a');

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'users successfully retrieved',
            'data' => null
        ]);
    }

    public function testCanUpdateProcessCategory() : void
    {
        $token = $this->getToken();

        $user = User::factory()->create([
            'first_name' => 'Sakhile',
            'last_name' => 'Van Heerden',
            'email' => 'sakhile@gmail.com'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->put('/api/user/update/'.$user->id, [
            'last_name' => 'Jekkings',
            'email' => 'tom.jekkings@gmail.com',
            'avatar' => ''
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'user successfully updated',
            'data' => null
        ]);

        // Is updated
        $response = $this->get('/api/user/'.$user->id);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'users successfully retrieved',
            'data' => [
                'first_name' => 'Sakhile',
                'last_name' => 'Jekkings'
            ]
        ]);
    }

    public function testCanNotUpdateProcessCategory() : void
    {
        $token = $this->getToken();

        $user = User::factory()->create([
            'first_name' => 'Tom',
            'last_name' => 'Jekkings',
            'email' => 'tom.jekkings@gmail.com',
            'password' => Hash::make('password'),
            'avatar' => ''
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->put('/api/user/update/'.$user->id, [
            'email' => 'testing wrong email'
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'message' => 'Input validation error',
            'errors' => []
        ]);
    }

    public function testCanCreateProcessCategory() : void
    {
        $token = $this->getToken();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->post('/api/user/create', [
            'first_name' => 'Tom 2',
            'last_name' => 'Jekkings 2',
            'email' => 'tom.jekkings@gmail.com',
            'password' => Hash::make('password'),
            'confirm_password' => Hash::make('password'),
            'avatar' => ''
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'user successfully created.',
            'data' => []
        ]);
    }

    public function testCanDeleteProcessCategory() : void
    {
        $token = $this->getToken();

        $user = User::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->delete('/api/user/delete/'.$user->id);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'user successfully deleted',
            'data' => []
        ]);
    }

    public function getToken()
    {
        User::factory([
            'email' => 'admin@negotium-solutions.com',
            'password' => 'password'
        ])->create();

        $response = $this->post('/api/auth/login',[
            'email' => 'admin@negotium-solutions.com',
            'password' => 'password'
        ]);

        return $response['data']['token'];
    }
}
