<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ActivityApiTest extends TestCase
{
    use RefreshDatabase;

    public function testCanGetAnActivity() : void
    {
        $token = $this->getToken();

        $activity = Activity::factory(['name' => 'Add Label'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->get('/api/activity/'.$activity->id);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'activities successfully retrieved',
            'data' => ['name' => 'Add Label']
        ]);
    }

    public function testCanGetActivities() : void
    {
        $token = $this->getToken();

        Activity::factory()->count(4)->create();

        Activity::factory(['name' => 'Add Combobox'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->get('/api/activity/');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertTrue(count($response['data']) === 5); // Number of users in the database, plus 1 created by getToken
    }

    public function testGetActivityNotFound() : void
    {
        $token = $this->getToken();

        Activity::factory(['name' => 'Add Dropdown not found'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->get('/api/activity/1000000000001');

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'activities successfully retrieved',
            'data' => null
        ]);
    }

    public function testCanUpdateActivity() : void
    {
        $token = $this->getToken();

        $activity = Activity::factory(['name' => 'Add Timestamp'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->put('/api/activity/update/'.$activity->id, [
            'name' => 'Add Datepicker'
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'activity successfully updated',
            'data' => null
        ]);

        // Is updated
        $response = $this->get('/api/activity/'.$activity->id);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'activities successfully retrieved',
            'data' => [
                'name' => 'Add Datepicker'
            ]
        ]);
    }

    public function testCanNotUpdateActivity() : void
    {
        $token = $this->getToken();

        $activity = Activity::factory(['name' => 'Add Button'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->put('/api/activity/update/'.$activity->id, [
            'name' => 00001
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'message' => 'Input validation error',
            'errors' => []
        ]);
    }

    public function testCanCreateActivity() : void
    {
        $token = $this->getToken();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->post('/api/activity/create/', [
            'name' => 'Add Textbox'
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'activity successfully created.',
            'data' => []
        ]);
    }

    public function testCanDeleteActivity() : void
    {
        $token = $this->getToken();

        $activity = Activity::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->delete('/api/activity/delete/'.$activity->id);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'activity successfully deleted',
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
