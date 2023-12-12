<?php

namespace Feature;

use App\Models\ProcessCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProcessCategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function testCanGetAProcessCategory() : void
    {
        $token = $this->getToken();

        $process_category = ProcessCategory::factory(['name' => 'HR Processes'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->get('/api/process-category/'.$process_category->id);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'process categories successfully retrieved',
            'data' => ['name' => 'HR Processes']
        ]);
    }

    public function testCanProcessCategories() : void
    {
        $token = $this->getToken();

        ProcessCategory::factory()->count(4)->create();

        ProcessCategory::factory(['name' => 'Project Allocation'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->get('/api/process-category/');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertTrue(count($response['data']) === 5); // Number of users in the database, plus 1 created by getToken
    }

    public function testGetUserNotFound() : void
    {
        $token = $this->getToken();

        ProcessCategory::factory(['name' => 'Project Allocation'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->get('/api/process-category/1000000000001');

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'process categories successfully retrieved',
            'data' => null
        ]);
    }

    public function testCanUpdateProcessCategory() : void
    {
        $token = $this->getToken();

        $processCategory = ProcessCategory::factory(['name' => 'Project Allocation'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->put('/api/process-category/update/'.$processCategory->id, [
            'name' => 'Resource Allocation'
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'process category successfully updated',
            'data' => null
        ]);

        // Is updated
        $response = $this->get('/api/process-category/'.$processCategory->id);
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
        $token = $this->getToken();

        $processCategory = ProcessCategory::factory(['name' => 'Project Allocation'])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->put('/api/process-category/update/'.$processCategory->id, [
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
        $token = $this->getToken();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->post('/api/process-category/create/', [
            'name' => 'Resource Allocation 2'
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'process category successfully created.',
            'data' => []
        ]);
    }

    public function testCanDeleteProcessCategory() : void
    {
        $token = $this->getToken();

        $processCategory = ProcessCategory::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])->delete('/api/process-category/delete/'.$processCategory->id);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'message' => 'process category successfully deleted',
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
