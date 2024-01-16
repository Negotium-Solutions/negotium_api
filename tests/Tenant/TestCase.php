<?php

namespace Tests\Tenant;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\CreatesApplication;
use App\Models\Tenant;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected $tenancy = true;
    protected $token = null;
    protected $tenant = null;

    public function setUp(): void
    {
        parent::setUp();

        if ($this->tenancy) {
            $this->initializeTenancy();
        }

        $this->token = $this->getToken();
        $this->tenant = $this->getTenant();
    }

    public function initializeTenancy()
    {
        $tenant = Tenant::create();

        tenancy()->initialize($tenant);
    }

    public function getToken() : string
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

    public function getTenant() : string
    {
        $tenant = Tenant::first();

        return $tenant->id;
    }
}
