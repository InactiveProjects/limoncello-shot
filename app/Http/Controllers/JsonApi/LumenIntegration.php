<?php namespace App\Http\Controllers\JsonApi;

use \Illuminate\Http\Request;
use \Illuminate\Http\Response;
use \Neomerx\Limoncello\Config\Config as C;
use \Neomerx\Limoncello\Http\FrameworkIntegration;

/**
 * @package Neomerx\LimoncelloShot
 */
class LumenIntegration extends FrameworkIntegration
{
    /**
     * @var Request
     */
    private $currentRequest;

    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        app()->configure(C::NAME);

        $config = config(C::NAME, []);
        /** @noinspection PhpUndefinedClassInspection */
        $config[C::JSON][C::JSON_URL_PREFIX] = \Request::getSchemeAndHttpHost();

        return $config;
    }

    /**
     * @inheritdoc
     *
     * @return Request
     */
    public function getCurrentRequest()
    {
        if ($this->currentRequest === null) {
            $this->currentRequest = app(Request::class);
        }

        return $this->currentRequest;
    }

    /**
     * @inheritdoc
     *
     * @return Response
     */
    public function createResponse($content, $statusCode, array $headers)
    {
        return new Response($content, $statusCode, $headers);
    }

    /**
     * @inheritdoc
     */
    public function getFromContainer($key)
    {
        return app($key);
    }

    /**
     * @inheritdoc
     */
    public function setInContainer($key, $value)
    {
        app()->instance($key, $value);
    }

    /**
     * @inheritdoc
     */
    public function hasInContainer($key)
    {
        return app()->resolved($key);
    }
}
