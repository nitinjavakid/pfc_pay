<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

use DMS\Service\Meetup\MeetupKeyAuthClient;

class MeetupApiProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->
            singleton('DMS\Service\Meetup\MeetupKeyAuthClient', function($app) {
                return MeetupKeyAuthClient::factory(array('key' => env('MEETUP_API_KEY')));
            });
    }
}
