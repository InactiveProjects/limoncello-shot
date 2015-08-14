<?php namespace App\Http\Middleware;

use \Closure;
use \App\Jwt\UserJwtCodecInterface;
use \Illuminate\Contracts\Auth\Guard as AuthInterface;
use \Neomerx\Limoncello\Contracts\IntegrationInterface;
use \Neomerx\Limoncello\Http\Middleware\BearerAuthMiddleware;

/**
 * @package Neomerx\LimoncelloShot
 */
class JsonApiJwtAuth extends BearerAuthMiddleware
{
    /**
     * Create a new filter instance.
     *
     * @param IntegrationInterface  $integration
     * @param UserJwtCodecInterface $codec
     * @param AuthInterface         $auth
     */
    public function __construct(IntegrationInterface $integration, UserJwtCodecInterface $codec, AuthInterface $auth)
    {
        $authenticateClosure = function ($jwt) use ($codec, $auth) {
            $isAuthenticated = false;

            if (($user = $codec->decode($jwt)) !== null) {
                $auth->login($user);
                $isAuthenticated = true;
            }

            return $isAuthenticated;
        };

        /** @var Closure|null $authorizeClosure */
        $authorizeClosure = null;

        /** @var string|null $realm */
        $realm = null;

        parent::__construct($integration, $authenticateClosure, $authorizeClosure, $realm);
    }
}
