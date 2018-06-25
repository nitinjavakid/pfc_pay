<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Instamojo\Instamojo;

class InstamojoProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Instamojo\Instamojo', function($app) {
            return new Instamojo(
                env('INSTAMOJO_KEY'),
                env('INSTAMOJO_AUTH_TOKEN'),
                env('INSTAMOJO_URL'));
            });
    }
}
