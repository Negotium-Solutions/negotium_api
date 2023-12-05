<?php

namespace Tests\Feature;

use App\Models\Process;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ProcessApiTest extends TestCase
{
    use RefreshDatabase;

    public function testCanGetAProcess() : void
    {
        $token = $this->getToken();

        $process = Process::factory(['name' => 'On-boarding'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->get('/api/process/'.$process->id);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'processes successfully retrieved',
            'data' => ['name' => 'On-boarding']
        ]);
    }

    public function testGetCanProcesses() : void
    {
        $token = $this->getToken();

        Process::factory()->count(4)->create();

        Process::factory(['name' => 'Off-boarding'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->get('/api/process/');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertTrue(count($response['data']) === 5); // Number of users in the database, plus 1 created by getToken
    }

    public function testGetUserNotFound() : void
    {
        $token = $this->getToken();

        Process::factory(['name' => 'Off-boarding'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->get('/api/process/1000000000001');

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'processes successfully retrieved',
            'data' => null
        ]);
    }

    public function testCanUpdateProcess() : void
    {
        $token = $this->getToken();

        $process = Process::factory(['name' => 'Off-boarding'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->put('/api/process/update/'.$process->id, [
            'name' => 'Equipment Allocation'
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'process successfully updated',
            'data' => null
        ]);

        // Is updated
        $response = $this->get('/api/process/'.$process->id);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'process successfully retrieved',
            'data' => [
                'name' => 'Equipment Allocation'
            ]
        ]);
    }

    public function testCanNotUpdateProcess() : void
    {
        $token = $this->getToken();

        $process = Process::factory(['name' => 'Equipment Allocation'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->put('/api/process/update/'.$process->id, [
            'name' => 1234
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'message' => 'Input validation error',
            'errors' => []
        ]);
    }

    public function testCanCreateProcess() : void
    {
        $token = $this->getToken();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->post('/api/process/create/', [
            'name' => 'Equipment Allocation 2'
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'process successfully created.',
            'data' => []
        ]);
    }

    public function testCanDeleteProcess() : void
    {
        $token = $this->getToken();

        $process = Process::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->delete('/api/process/delete/'.$process->id);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'process successfully deleted',
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
