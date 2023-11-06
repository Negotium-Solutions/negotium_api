<?php

namespace Rikscss\BaseApi;

use Illuminate\Support\ServiceProvider;

class BaseApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    /*
    public function register(): void
    {
        //
    }
    */
    /**
     * Bootstrap services.
     */
    /*
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
    */

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'base-api-controller');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'base-api-controller');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('base-api.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/base-api-controller'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/base-api-controller'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/base-api-controller'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
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
            return new BaseApi;
        });
    }
}
