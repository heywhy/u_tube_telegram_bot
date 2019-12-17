<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Telegram\Bot\Laravel\Facades\Telegram;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->environment('production')) {
            Telegram::setWebHook(['url' => env('APP_URL') . '/webhook']);
            $this->getGoogleCredentials();
        }
    }

    protected function getGoogleCredentials()
    {
        $url = env('GOOGLE_CRED_URL');
        $file = base_path('service-accounts.json');
        if (!file_exists($file) && $url != null) {
            $contents = file_get_contents($url);
            file_put_contents($file, $contents);
        }
    }
}
