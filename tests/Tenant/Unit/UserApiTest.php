<?php

namespace Tests\Tenant\Unit;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Tests\Tenant\TestCase;

class UserApiTest extends TestCase
{
    public function testCanGetAUser() : void
    {
        $user = User::factory(['first_name' => 'Tom'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->get('/api/'.$this->tenant.'/user/'.$user->id);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'users successfully retrieved',
            'data' => ['first_name' => 'Tom']
        ]);
    }

    public function testCanGetUsers() : void
    {
        User::factory()->count(6)->create();

        User::factory()->create([
            'first_name' => 'Sakhile',
            'last_name' => 'Van Heerden'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->get('/api/'.$this->tenant.'/user');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertTrue(count($response['data']) === 8); // Number of users in the database, plus 1 created by getToken
    }

    public function testGetUserNotFound() : void
    {
        User::factory()->create([
            'first_name' => 'Sakhile',
            'last_name' => 'Van Heerden'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->get('/api/'.$this->tenant.'/user/9a90bb05-4a72-4b82-8e60-2b069a15d34a');

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'users successfully retrieved',
            'data' => null
        ]);
    }

    public function testCanUpdateUser() : void
    {
        $user = User::factory()->create([
            'first_name' => 'Sakhile',
            'last_name' => 'Van Heerden',
            'email' => 'sakhile@gmail.com'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->put('/api/'.$this->tenant.'/user/update/'.$user->id, [
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
        $response = $this->get('/api/'.$this->tenant.'/user/'.$user->id);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'users successfully retrieved',
            'data' => [
                'first_name' => 'Sakhile',
                'last_name' => 'Jekkings'
            ]
        ]);
    }

    public function testCanNotUpdateUser() : void
    {
        $user = User::factory()->create([
            'first_name' => 'Tom',
            'last_name' => 'Jekkings',
            'email' => 'tom.jekkings@gmail.com',
            'password' => Hash::make('password'),
            'avatar' => ''
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->put('/api/'.$this->tenant.'/user/update/'.$user->id, [
            'email' => 'testing wrong email'
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'message' => 'Input validation error',
            'errors' => []
        ]);
    }

    public function testCanCreateUser() : void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->post('/api/'.$this->tenant.'/user/create', [
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

    public function testCanDeleteUser() : void
    {
        $user = User::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->delete('/api/'.$this->tenant.'/user/delete/'.$user->id);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'user successfully deleted',
            'data' => []
        ]);
    }
}
