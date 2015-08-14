<?php

namespace App\Providers;

use \App\Jwt\UserJwtCodec;
use \App\Jwt\UserJwtCodecInterface;
use \Illuminate\Support\ServiceProvider;
use \App\Http\Controllers\JsonApi\LumenIntegration;
use \Neomerx\Limoncello\Contracts\IntegrationInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(IntegrationInterface::class, function () {
            return new LumenIntegration();
        });
        $this->app->bind(UserJwtCodecInterface::class, function () {
            return new UserJwtCodec();
        });
    }
}
