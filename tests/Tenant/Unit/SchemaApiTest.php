<?php

namespace Tests\Tenant\Unit;

use App\Models\Schema;
use Illuminate\Http\Response;
use Illuminate\Queue\Failed\CountableFailedJobProvider;
use Tests\Tenant\TestCase;

class SchemaApiTest extends TestCase
{
    public function testCanGetASchema() : void
    {
        $schema = Schema::factory([
            'name' => 'Employee'
        ])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->get('/api/'.$this->tenant.'/schema/'.$schema->id);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'schemas successfully retrieved',
            'data' => ['name' => 'Employee']
        ]);

        $this->assertTrue(true);
    }

    public function testCanGetSchemas() : void
    {
        Schema::factory()->count(2)->create();

        Schema::factory([
            'name' => 'Client'
        ])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->get('/api/'.$this->tenant.'/schema');

        $response->assertStatus(Response::HTTP_OK);
        
        $this->assertTrue(count($response['data']) === 3); // Number of schemas in the database,
    }

    public function testGetSchemaNotFound() : void
    {
        Schema::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->get('/api/'.$this->tenant.'/schema/9999999999999999');

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'schemas successfully retrieved',
            'data' => null
        ]);
    }

    public function testCanUpdateSchema() : void
    {
        $json_payload = file_get_contents(public_path('tests/create_schema_payload.json'));
        $payload = json_decode($json_payload, true);

        $schema = Schema::factory()->create([
            'name' => 'Employee'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->put('/api/'.$this->tenant.'/schema/update/'.$schema->id, 
        $payload);
        
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'schema successfully updated',
            'data' => ['table' => 'profile_manager']
        ]);

        // Is updated
        $response = $this->get('/api/'.$this->tenant.'/schema/'.$schema->id);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'schemas successfully retrieved',
            'data' => [
                'name' => 'Staff'
            ]
        ]);
    }

    public function testCanNotUpdateSchema() : void
    {
        $schema = Schema::factory()->create([
            'name' => 'Employee'
        ]);

        $payload = Schema::factory()->create([
            'name' => 'Staff'
        ]);
        $schemaPayload = json_decode(json_encode($payload), true);
        //dd($mySchemaArray);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->put('/api/'.$this->tenant.'/schema/update/'.$schema->id, $schemaPayload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'message' => 'Input validation error',
            'errors' => []
        ]);
    }

    public function testCanCreateSchema() : void
    {
        $json_payload = file_get_contents(public_path('tests/create_schema_payload.json'));
        $payload = json_decode($json_payload, true);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->post('/api/'.$this->tenant.'/schema/create', $payload);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'schema successfully created.',
            'data' => ['table' => 'profile_manager']
        ]);
    }

    public function testCanDeleteSchema() : void
    {
        $schema = Schema::factory()->create([
            'name' => 'ToDeleteEmployee'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->delete('/api/'.$this->tenant.'/schema/delete/'.$schema->id);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'schema successfully deleted',
            'data' => []
        ]);
    }

    public function testCanGetSchemaColumns() : void
    {
        $schema = Schema::factory([
            'name' => 'Employee'
        ])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->get('/api/'.$this->tenant.'/schema/'.$schema->id);

        $response->assertStatus(Response::HTTP_OK);
        $schemaColumns = json_decode(($response['data']['columns']), true);

        $this->assertTrue(count($schemaColumns) === 4);
    }
}

