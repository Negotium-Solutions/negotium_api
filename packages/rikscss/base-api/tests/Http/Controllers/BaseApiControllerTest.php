<?php

namespace Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Rikscss\BaseApiController\Http\Controllers\Api\UserController;
use Rikscss\BaseApiController\Models\User;
use Rikscss\BaseApi\Tests\TestCase;

class BaseApiControllerTest extends TestCase
{
    public function testCanGetAUser() : void
    {
        Route::get('/api/user/{id?}', [UserController::class, 'get'])
        ->name('api.user')
        ->middleware('auth:sanctum');

        $user = new User();
        $user->first_name = 'Tom';
        $user->last_name = 'Jekkings';
        $user->email = 'tom.jekkings@gmail.com';
        $user->password = Hash::make('password');
        $user->avatar = '';
        $user->save();

        $response = $this->get('/api/user/'.$user->id);

        $this->assertTrue($response['data']['first_name'] == 'Tom');

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'users successfully retrieved',
            'data' => ['first_name' => 'Tom']
        ]);
    }

    public function testCanGetUsers() : void
    {
        Route::get('/api/user/{id?}', [UserController::class, 'get'])->name('api.user');

        // $users = User::factory()->count(3)->make(); // Todo: Google why we can't use factories classes as they say they are not found

        $user1 = new User();
        $user1->first_name = 'Tom';
        $user1->last_name = 'Jekkings';
        $user1->email = 'tom.jekkings@gmail.com';
        $user1->password = Hash::make('password');
        $user1->avatar = '';
        $user1->save();

        $user2 = new User();
        $user2->first_name = 'Thato';
        $user2->last_name = 'Van Heerden';
        $user2->email = 'thato.heerden@gmail.com';
        $user2->password = Hash::make('password');
        $user2->avatar = '';
        $user2->save();

        $response = $this->get('/api/user');

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'users successfully retrieved',
            'data' => [
                ['first_name' => 'Tom'],
                ['first_name' => 'Thato']
            ]
        ]);
    }

    public function testGetUserNotFound() : void
    {
        Route::get('/api/user/{id?}', [UserController::class, 'get'])->name('api.user');

        $user = new User();
        $user->first_name = 'Tom';
        $user->last_name = 'Jekkings';
        $user->email = 'tom.jekkings@gmail.com';
        $user->password = Hash::make('password');
        $user->avatar = '';
        $user->save();

        $response = $this->get('/api/user/1');

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'users successfully retrieved',
            'data' => null
        ]);
    }

    public function testCanUpdateUser() : void
    {
        Route::put('/api/user/update/{id}', [UserController::class, 'update'])->name('api.user.update');
        Route::get('/api/user/{id?}', [UserController::class, 'get'])->name('api.user');

        $user = new User();
        $user->first_name = 'Tom';
        $user->last_name = 'Jekkings';
        $user->email = 'tom.jekkings@gmail.com';
        $user->password = Hash::make('password');
        $user->avatar = '';
        $user->save();

        $response = $this->put('/api/user/update/'.$user->id, [
            'first_name' => 'Tom 2',
            'last_name' => 'Jekkings 2',
            'email' => 'tom.jekkings@gmail.com',
            'password' => Hash::make('password'),
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
            'data' => ['first_name' => 'Tom 2']
        ]);
    }

    public function testCanNotUpdateUser() : void
    {
        Route::put('/api/user/update/{id}', [UserController::class, 'update'])->name('api.user.update');

        $user = new User();
        $user->first_name = 'Tom';
        $user->last_name = 'Jekkings';
        $user->email = 'tom.jekkings@gmail.com';
        $user->password = Hash::make('password');
        $user->avatar = '';
        $user->save();

        $response = $this->put('/api/user/update/'.$user->id);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'message' => 'Input validation error',
            'errors' => []
        ]);
    }

    public function testCanCreateUser() : void
    {
        Route::post('/api/user/create', [UserController::class, 'create'])->name('api.user.create');

        $response = $this->post('/api/user/create', [
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
        Route::post('/api/user/store', [UserController::class, 'store'])->name('api.user.store');
        Route::delete('/api/user/delete/{id}', [UserController::class, 'destroy'])->name('api.user.delete');

        $response = $this->post('/api/user/store', [
            'first_name' => 'Tom 2',
            'last_name' => 'Jekkings 2',
            'email' => 'tom.jekkings@gmail.com',
            'password' => Hash::make('password'),
            'avatar' => ''
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $response = $this->delete('/api/user/delete/'.$response['data']['id']);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'user successfully deleted',
            'data' => []
        ]);
    }
}
