<?php

namespace Tests\Tenant\Unit;

use App\Models\Tenant\ProcessStep;
use Illuminate\Http\Response;
use Tests\Tenant\TestCase;

class ProcessStepApiTest extends TestCase
{
    public function testCanGetAProcessStep() : void
    {
        $process_step = ProcessStep::factory(['name' => 'Step 01'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->get('/api/'.$this->tenant.'/process-step/'.$process_step->id);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'process steps(s) successfully retrieved',
            'data' => ['name' => 'Step 01']
        ]);
    }

    public function testCanGetProcessSteps() : void
    {
        ProcessStep::factory()->count(4)->create();

        ProcessStep::factory(['name' => 'Step 01'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->get('/api/'.$this->tenant.'/process-step/');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertTrue(count($response['data']) === 5); // Number of users in the database, plus 1 created by getToken
    }

    public function testGetProcessStepNotFound() : void
    {
        ProcessStep::factory(['name' => 'Step 01'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->get('/api/'.$this->tenant.'/process-step/1000000000001');

        $response->assertStatus(Response::HTTP_NOT_FOUND);

        $response->assertJson([
            'message' => 'No process step record(s) found',
            'data' => null
        ]);
    }

    public function testCanUpdateProcessStep() : void
    {
        $processStep = ProcessStep::factory(['name' => 'Step 01'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->put('/api/'.$this->tenant.'/process-step/update/'.$processStep->id, [
            'name' => 'Step 02'
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'process step successfully updated',
            'data' => null
        ]);

        // Is updated
        $response = $this->get('/api/'.$this->tenant.'/process-step/'.$processStep->id);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'process steps(s) successfully retrieved',
            'data' => [
                'name' => 'Step 02'
            ]
        ]);
    }

    public function testCanNotUpdateProcessStep() : void
    {
        $processStep = ProcessStep::factory(['name' => 'Step 01'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->put('/api/'.$this->tenant.'/process-step/update/'.$processStep->id, [
            'name' => 1234
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'message' => 'Input validation error',
            'errors' => []
        ]);
    }

    public function testCanCreateProcessStep() : void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->post('/api/'.$this->tenant.'/process-step/create/', [
            'name' => 'Step 01',
            'process_id' => 1
        ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertJson([
            'message' => 'process step successfully created.',
            'data' => []
        ]);
    }

    public function testCanDeleteProcessStep() : void
    {
        $processStep = ProcessStep::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->delete('/api/'.$this->tenant.'/process-step/delete/'.$processStep->id);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
