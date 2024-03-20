<?php

namespace Tests\Tenant\Unit;

use App\Models\Process;
use Illuminate\Http\Response;
use Tests\Tenant\TestCase;

class ProcessApiTest extends TestCase
{
    public function testCanGetAProcess() : void
    {
        $process = Process::factory(['name' => 'On-boarding'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->get('/api/'.$this->tenant.'/process/'.$process->id);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'processes successfully retrieved',
            'data' => ['name' => 'On-boarding']
        ]);
    }

    public function testCanGetProcesses() : void
    {
        Process::factory()->count(4)->create();

        Process::factory(['name' => 'Off-boarding'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->get('/api/'.$this->tenant.'/process/');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertTrue(count($response['data']) === 5); // Number of users in the database, plus 1 created by getToken
    }

    public function testGetProcessNotFound() : void
    {
        Process::factory(['name' => 'Off-boarding'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->get('/api/'.$this->tenant.'/process/1000000000001');

        $response->assertStatus(Response::HTTP_NOT_FOUND);

        $response->assertJson([
            'message' => 'No process record(s) found',
            'data' => null
        ]);
    }

    public function testCanUpdateProcess() : void
    {
        $process = Process::factory(['name' => 'Off-boarding'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->put('/api/'.$this->tenant.'/process/update/'.$process->id, [
            'name' => 'Equipment Allocation'
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'process successfully updated',
            'data' => null
        ]);

        // Is updated
        $response = $this->get('/api/'.$this->tenant.'/process/'.$process->id);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'processes successfully retrieved',
            'data' => [
                'name' => 'Equipment Allocation'
            ]
        ]);
    }

    public function testCanNotUpdateProcess() : void
    {
        $process = Process::factory(['name' => 'Equipment Allocation'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->put('/api/'.$this->tenant.'/process/update/'.$process->id, [
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
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->post('/api/'.$this->tenant.'/process/create/', [
            'name' => 'Equipment Allocation 2',
            'process_category_id' => 1
        ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertJson([
            'message' => 'process successfully created.',
            'data' => []
        ]);
    }

    public function testCanDeleteProcess() : void
    {
        $process = Process::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->delete('/api/'.$this->tenant.'/process/delete/'.$process->id);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
