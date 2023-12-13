<?php

namespace Tests\Feature;

use App\Models\Step;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StepApiTest extends TestCase
{
    use RefreshDatabase;

    public function testCanGetAStep() : void
    {
        $token = $this->getToken();

        $step = Step::factory(['name' => 'Personal Details'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->get('/api/step/'.$step->id);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'steps successfully retrieved',
            'data' => ['name' => 'Personal Details']
        ]);
    }

    public function testCanGetSteps() : void
    {
        $token = $this->getToken();

        Step::factory()->count(4)->create();

        Step::factory(['name' => 'Personal Details'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->get('/api/step/');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertTrue(count($response['data']) === 5); // Number of users in the database, plus 1 created by getToken
    }

    public function testGetStepNotFound() : void
    {
        $token = $this->getToken();

        Step::factory(['name' => 'Background Check'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->get('/api/step/1000000000001');

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'steps successfully retrieved',
            'data' => null
        ]);
    }

    public function testCanUpdateStep() : void
    {
        $token = $this->getToken();

        $step = Step::factory(['name' => 'Health Details'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->put('/api/step/update/'.$step->id, [
            'name' => 'Medical Aid Details'
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'step successfully updated',
            'data' => null
        ]);

        // Is updated
        $response = $this->get('/api/step/'.$step->id);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'steps successfully retrieved',
            'data' => [
                'name' => 'Medical Aid Details'
            ]
        ]);
    }

    public function testCanNotUpdateStep() : void
    {
        $token = $this->getToken();

        $step = Step::factory(['name' => 'Next Of Kin'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->put('/api/step/update/'.$step->id, [
            'name' => 00001
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'message' => 'Input validation error',
            'errors' => []
        ]);
    }

    public function testCanCreateStep() : void
    {
        $token = $this->getToken();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->post('/api/step/create/', [
            'name' => 'Next Of Kin Details'
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'step successfully created.',
            'data' => []
        ]);
    }

    public function testCanDeleteStep() : void
    {
        $token = $this->getToken();

        $step = Step::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->delete('/api/step/delete/'.$step->id);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'step successfully deleted',
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
