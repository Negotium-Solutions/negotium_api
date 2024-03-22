<?php

namespace Tests\Tenant\Unit;

use App\Models\ProcessCategory;
use Illuminate\Http\Response;
use Tests\Tenant\TestCase;

class ProcessCategoryApiTest extends TestCase
{
    public function testCanGetAProcessCategory() : void
    {
        $process_category = ProcessCategory::factory(['name' => 'HR Processes'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->get('/api/'.$this->tenant.'/process-category/'.$process_category->id);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'process categories successfully retrieved',
            'data' => ['name' => 'HR Processes']
        ]);
    }

    public function testCanGetProcessCategories() : void
    {
        ProcessCategory::factory()->count(4)->create();

        ProcessCategory::factory(['name' => 'Project Allocation'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->get('/api/'.$this->tenant.'/process-category/');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertTrue(count($response['data']) === 5); // Number of users in the database, plus 1 created by getToken
    }

    public function testGetProcessCategoryNotFound() : void
    {
        ProcessCategory::factory(['name' => 'Project Allocation'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->get('/api/'.$this->tenant.'/process-category/1000000000001');

        $response->assertStatus(Response::HTTP_NOT_FOUND);

        $response->assertJson([
            'message' => 'No process category record(s) found',
            'data' => null
        ]);
    }

    public function testCanUpdateProcessCategory() : void
    {
        $processCategory = ProcessCategory::factory(['name' => 'Project Allocation'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->put('/api/'.$this->tenant.'/process-category/update/'.$processCategory->id, [
            'name' => 'Resource Allocation'
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'process category successfully updated',
            'data' => null
        ]);

        // Is updated
        $response = $this->get('/api/'.$this->tenant.'/process-category/'.$processCategory->id);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'process categories successfully retrieved',
            'data' => [
                'name' => 'Resource Allocation'
            ]
        ]);
    }

    public function testCanNotUpdateProcessCategory() : void
    {
        $processCategory = ProcessCategory::factory(['name' => 'Project Allocation'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->put('/api/'.$this->tenant.'/process-category/update/'.$processCategory->id, [
            'name' => 1234
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'message' => 'Input validation error',
            'errors' => []
        ]);
    }

    public function testCanCreateProcessCategory() : void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->post('/api/'.$this->tenant.'/process-category/create/', [
            'name' => 'Resource Allocation 2'
        ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertJson([
            'message' => 'process category successfully created.',
            'data' => []
        ]);
    }

    public function testCanDeleteProcessCategory() : void
    {
        $processCategory = ProcessCategory::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            'Accept' => 'application/json'
        ])->delete('/api/'.$this->tenant.'/process-category/delete/'.$processCategory->id);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
/*
        $response->assertJson([
            'message' => 'process category successfully deleted',
            'data' => []
        ]); */
    }
}
