<?php

namespace Tests\Tenant\Unit;

use App\Models\Schema;
use Illuminate\Http\Response;
use Tests\Tenant\TestCase;

class SchemaApiTest extends TestCase
{
    public function testCanGetASchema() : void
    {
        $mySchema = Schema::factory([
            'name' => 'Schema1'
        ])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->get('/api/'.$this->tenant.'/schema/'.$mySchema->id);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'schemas successfully retrieved',
            'data' => ['name' => 'Schema1']
        ]);

        $this->assertTrue(true);
    }

    public function testCanGetSchemas() : void
    {
        Schema::factory()->count(4)->create();

        $mySchema = Schema::factory([
            'name' => 'Schema1'
        ])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->get('/api/'.$this->tenant.'/schema');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertTrue(count($response['data']) === 6); // Number of users in the database, plus 1 created by getToken
    }

    public function testGetSchemaNotFound() : void
    {
        Schema::factory()->create([
            'name' => 'svanheerden',
            'domain' => 'svanheerden.co.za',
            'first_name' => 'Sakhile',
            'last_name' => 'Van Heerden',
            'email' => 'svanheerden@gmail.com'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->get('/api/'.$this->tenant.'/schema/9a90bb05-4a72-4b82-8e60-2b069a15d34a');

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'schemas successfully retrieved',
            'data' => null
        ]);
    }

    public function testCanUpdateSchema() : void
    {
        $schema = Schema::factory()->create([
            'name' => 'svanheerden',
            'domain' => 'svanheerden.co.za',
            'first_name' => 'Sakhile',
            'last_name' => 'Van Heerden',
            'email' => 'svanheerden@gmail.com'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->put('/api/'.$this->tenant.'/schema/update/'.$schema->id, [
            'name' => 'svanheerden',
            'domain' => 'svanheerden.co.za',
            'first_name' => 'Sakhile',
            'last_name' => 'Jekkings',
            'email' => 'tom.jekkings@gmail.com'
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'schema successfully updated',
            'data' => null
        ]);

        // Is updated
        $response = $this->get('/api/'.$this->tenant.'/schema/'.$schema->id);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'schemas successfully retrieved',
            'data' => [
                'first_name' => 'Sakhile',
                'last_name' => 'Jekkings'
            ]
        ]);
    }

    public function testCanNotUpdateSchema() : void
    {
        $schema = Schema::factory()->create([
            'name' => 'tomjekkings',
            'domain' => 'tomjekkings.co.za',
            'first_name' => 'Tom',
            'last_name' => 'Jekkings',
            'email' => 'tom.jekkings@gmail.com'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->put('/api/'.$this->tenant.'/schema/update/'.$schema->id, [
            'email' => 'testing wrong email'
        ]);

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
            'message' => 'schema successfully created',
            'data' => []
        ]);
    }

    public function testCanDeleteSchema() : void
    {
        $schema = Schema::factory()->create();

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
}

