<?php

namespace App\Providers;

use Google_Client;
use Google_Service;
use Google_Service_YouTube;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class GoogleApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Google_Client::class, function () {
            $client = new Google_Client();

            $client->setDeveloperKey(env('YOUTUBE_API_KEY'));

            return $client;
        });

        // $this->app->singleton(Google_Service_YouTube::class, fn (Application $app) => new Google_Service_YouTube($app->make(Google_Client::class)));
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
