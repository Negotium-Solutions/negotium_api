<?php

namespace Rikscss\BaseApi;

use Illuminate\Support\ServiceProvider;
use Rikscss\BaseApi\Http\Controllers\Api\BaseApiLogController;

class BaseApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('base-api.php')
            ], 'config');

            $this->publishes([
                __DIR__.'/../database/migrations/' => database_path('migrations')
            ], 'migrations');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'base-api');

        // Register the main class to use with the facade
        $this->app->singleton('base-api', function () {
            return new BaseApiLogController;
        });
    }
}
