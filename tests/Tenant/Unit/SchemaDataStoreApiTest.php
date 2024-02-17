<?php

namespace Tests\Tenant\Unit;

use App\Models\Schema;
use App\Models\SchemaDataStore;
use Illuminate\Http\Response;
use Tests\Tenant\TestCase;

class SchemaDataStoreApiTest extends TestCase
{
    public function testCanGetASchemaDataStore() : void
    {
        $json_payload = file_get_contents(public_path('tests/create_schemadatastore_payload.json'));
        $payload = json_decode($json_payload, true);
        $schemaDataStore = SchemaDataStore::factory($payload)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->get('/api/'.$this->tenant.'/schema-data-store/'.$schemaDataStore->id);
        dd($response);
        $response->assertStatus(Response::HTTP_OK);
        
        $response->assertJson([
            'message' => 'Items successfully retrieved',
            'data' => ['schema_id' => 1]
        ]);

        $this->assertTrue(true);
    }

    public function testCanGetSchemasDataStore() : void
    {
        SchemaDataStore::factory()->count(2)->create();

        $json_payload = file_get_contents(public_path('tests/create_schemadatastore_payload.json'));
        $payload = json_decode($json_payload, true);
        SchemaDataStore::factory($payload)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->get('/api/'.$this->tenant.'/schema-data-store');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertTrue(count($response['data']) === 3); // Number of schemas in the database,
    }

    public function testGetSchemaDataStoreNotFound() : void
    {
        SchemaDataStore::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->get('/api/'.$this->tenant.'/schema-data-store/9999999999999999');

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'Items successfully retrieved',
            'data' => null
        ]);
    }

    public function testCanUpdateSchemaDataStore() : void
    {
        $schemaDataStore = SchemaDataStore::factory()->create([
            'shema_id' => 1
        ]);

        $json_payload = file_get_contents(public_path('tests/create_schemadatastore_payload.json'));
        $payload = json_decode($json_payload, true);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->put('/api/'.$this->tenant.'/schema-data-store/update/'.$schemaDataStore->id, 
        $payload);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'Schema data successfully stored',
            'data' => null
        ]);

        // Is updated
        $response = $this->get('/api/'.$this->tenant.'/schema-data-store/'.$schemaDataStore->id);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'Schema data successfully stored',
            'data' => [
                'schema_id' => $payload['schema_id']
            ]
        ]);
    }

    public function testCanNotUpdateSchemaDataStore() : void
    {
        $schemaDataStore = SchemaDataStore::factory()->create([
            'schema_id' => 1
        ]);

        $mySchemaDataStore = SchemaDataStore::factory()->create([
            'schema_id' => 999
        ]);
        $mySchemaDataStoreArray = json_decode(json_encode($mySchemaDataStore), true);
        //dd($mySchemaArray);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->put('/api/'.$this->tenant.'/schema-data-store/update/'.$schemaDataStore->id, $mySchemaDataStoreArray);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'message' => 'Input validation error',
            'errors' => []
        ]);
    }

    public function testCanCreateSchemaDataStore() : void
    {
        //$schema = Schema::factory()->create([]);
        //$schemaDataStore = SchemaDataStore::factory([
        //    'schema_id' => $schema->id,
        //    'data' => $schema->columns
        //])->create([]);
        
        $json_payload = file_get_contents(public_path('tests/create_schemadatastore_payload.json'));
        $payload = json_decode($json_payload, true);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->post('/api/'.$this->tenant.'/schema-data-store/create', $payload);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'Schema data successfully created',
            'data' => []
        ]);
    }

    public function testCanDeleteSchemaDataStore() : void
    {
        $schemaDataStore = SchemaDataStore::factory()->create([
            'schema_id' => 1
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->delete('/api/'.$this->tenant.'/schema-data-store/delete/'.$schemaDataStore->schema_id);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'Item successfully deleted',
            'data' => []
        ]);
    }


}
