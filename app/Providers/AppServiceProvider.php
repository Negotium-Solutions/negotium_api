<?php

namespace App\Providers;

use App\Repositories\SchemaRepository;
use App\Repositories\SchemaRepositoryInterface;
use App\Services\SchemaServce;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SchemaRepositoryInterface::class, SchemaRepository::class);
        $this->app->bind(SchemaServce::class, function ($app) {
            return new SchemaServce($app->make(SchemaRepositoryInterface::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
