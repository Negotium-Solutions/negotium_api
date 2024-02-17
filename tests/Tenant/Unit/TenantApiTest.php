<?php

namespace Tests\Tenant\Unit;

use App\Models\Tenant;
use Illuminate\Http\Response;
use Tests\Tenant\TestCase;

class TenantApiTest extends TestCase
{
    public function testCanGetATenant() : void
    {
        $myTenant = Tenant::factory([
            'name' => 'Phaks',
            'domain' => 'Phaks.co.za',
            'first_name' => 'Skhahla',
            'last_name' => 'Phakula',
            'email' => 'skhahla@phaks.co.za'
        ])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->get('/api/tenant/'.$myTenant->id);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'tenants successfully retrieved',
            'data' => ['name' => 'Phaks']
        ]);
    }

    public function testCanGetTenants() : void
    {
        Tenant::factory()->count(4)->create();

        Tenant::factory()->create([
            'name' => 'svanheerden',
            'domain' => 'svanheerden.co.za',
            'first_name' => 'Sakhile',
            'last_name' => 'Van Heerden',
            'email' => 'svanheerden@gmail.com'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->get('/api/tenant');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertTrue(count($response['data']) === 6); // Number of users in the database, plus 1 created by getToken
    }

    public function testGetTenantNotFound() : void
    {
        Tenant::factory()->create([
            'name' => 'svanheerden',
            'domain' => 'svanheerden.co.za',
            'first_name' => 'Sakhile',
            'last_name' => 'Van Heerden',
            'email' => 'svanheerden@gmail.com'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->get('/api/tenant/9a90bb05-4a72-4b82-8e60-2b069a15d34a');

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'tenants successfully retrieved',
            'data' => null
        ]);
    }

    public function testCanUpdateTenant() : void
    {
        $tenant = Tenant::factory()->create([
            'name' => 'svanheerden',
            'domain' => 'svanheerden.co.za',
            'first_name' => 'Sakhile',
            'last_name' => 'Van Heerden',
            'email' => 'svanheerden@gmail.com'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->put('/api/tenant/update/'.$tenant->id, [
            'name' => 'svanheerden',
            'domain' => 'svanheerden.co.za',
            'first_name' => 'Sakhile',
            'last_name' => 'Jekkings',
            'email' => 'tom.jekkings@gmail.com'
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'tenant successfully updated',
            'data' => null
        ]);

        // Is updated
        $response = $this->get('/api/tenant/'.$tenant->id);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'tenants successfully retrieved',
            'data' => [
                'first_name' => 'Sakhile',
                'last_name' => 'Jekkings'
            ]
        ]);
    }

    public function testCanNotUpdateTenant() : void
    {
        $tenant = Tenant::factory()->create([
            'name' => 'tomjekkings',
            'domain' => 'tomjekkings.co.za',
            'first_name' => 'Tom',
            'last_name' => 'Jekkings',
            'email' => 'tom.jekkings@gmail.com'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->put('/api/tenant/update/'.$tenant->id, [
            'email' => 'testing wrong email'
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'message' => 'Input validation error',
            'errors' => []
        ]);
    }

    public function testCanCreateTenant() : void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->post('/api/tenant/create', [
            'name' => 'tomjekkings',
            'domain' => 'tomjekkings.co.za',
            'first_name' => 'Tom',
            'last_name' => 'Jekkings',
            'email' => 'tom.jekkings@gmail.com',
            "password" => "password"
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'tenant successfully created',
            'data' => []
        ]);
    }

    public function testCanDeleteTenant() : void
    {
        $tenant = Tenant::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->delete('/api/tenant/delete/'.$tenant->id);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'tenant successfully deleted',
            'data' => []
        ]);
    }
}
