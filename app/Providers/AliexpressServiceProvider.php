<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AliexpressService;

class AliexpressServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(AliexpressService::class, function ($app) {
            return new AliexpressService();
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
