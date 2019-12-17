<?php

namespace App\Providers;

use Google_Client;
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
            $client->useApplicationDefaultCredentials();
            $client->setScopes(['https://www.googleapis.com/auth/youtube']);

            return $client;
        });
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
