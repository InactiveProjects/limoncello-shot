<?php namespace App\Http\Middleware;

use \Closure;
use \Illuminate\Contracts\Auth\Guard;
use \Neomerx\Limoncello\Contracts\IntegrationInterface;
use \Neomerx\Limoncello\Http\Middleware\BasicAuthMiddleware;

/**
 * @package Neomerx\LimoncelloShot
 */
class JsonApiBasicAuth extends BasicAuthMiddleware
{
    /**
     * Create a new filter instance.
     *
     * @param IntegrationInterface $integration
     * @param Guard                $auth
     */
    public function __construct(IntegrationInterface $integration, Guard $auth)
    {
        $authenticateClosure = function ($login, $password) use ($auth) {
            $isAuthenticated = $auth->attempt([
                'email'    => $login,
                'password' => $password,
            ]);

            return $isAuthenticated;
        };

        /** @var Closure|null $authorizeClosure */
        $authorizeClosure = null;

        /** @var string|null $realm */
        $realm = null;

        parent::__construct($integration, $authenticateClosure, $authorizeClosure, $realm);
    }
}
