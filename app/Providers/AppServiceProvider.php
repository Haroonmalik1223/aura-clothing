<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        $isHttps = config('app.env') === 'production'
            || request()->header('x-forwarded-proto') === 'https'
            || (request()->header('cf-visitor') && str_contains(request()->header('cf-visitor'), 'https'))
            || request()->isSecure();

        if ($isHttps) {
            \Illuminate\Support\Facades\URL::forceScheme('https');

            request()->server->set('HTTPS', 'on');
            request()->server->set('SERVER_PORT', 443);

            $host = request()->getHost();
            if ($host && !in_array($host, ['localhost', '127.0.0.1'])) {
                \Illuminate\Support\Facades\URL::forceRootUrl('https://' . $host);
            }
        }
    }
}
