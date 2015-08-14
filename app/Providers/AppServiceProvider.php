<?php

namespace App\Providers;

use \App\Jwt\UserJwtCodec;
use \App\Jwt\UserJwtCodecInterface;
use \Illuminate\Support\ServiceProvider;
use \App\Http\Controllers\JsonApi\LumenIntegration;
use \Neomerx\Limoncello\Http\AppServiceProviderTrait;
use \Neomerx\Limoncello\Contracts\IntegrationInterface;

/**
 * @package Neomerx\LimoncelloShot
 */
class AppServiceProvider extends ServiceProvider
{
    use AppServiceProviderTrait;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $integration = new LumenIntegration();

        $this->registerResponses($integration);
        $this->registerCodecMatcher($integration);
        $this->registerExceptionThrower($integration);

        $this->app->bind(IntegrationInterface::class, function () {
            return new LumenIntegration();
        });
        $this->app->bind(UserJwtCodecInterface::class, function () {
            return new UserJwtCodec();
        });
    }
}
