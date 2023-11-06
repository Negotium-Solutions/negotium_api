<?php

namespace Rikscss\BaseApi\Tests;

use Rikscss\BaseApi\BaseApiServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', ['--database' => 'testing'])->run();
    }

    protected function getPackageProviders($app)
    {
        return [
            BaseApiServiceProvider::class
        ];
    }
}
