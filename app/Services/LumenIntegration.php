<?php namespace App\Services;

use \Illuminate\Http\Request;
use \Illuminate\Http\Response;
use \Neomerx\Limoncello\Config\Config;
use \Neomerx\Limoncello\Http\FrameworkIntegration;
use \Neomerx\JsonApi\Contracts\Parameters\SupportedExtensionsInterface;

/**
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
        app()->configure(Config::NAME);
        return config(Config::NAME, []);
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
     */
    public function declareSupportedExtensions(SupportedExtensionsInterface $extensions)
    {
        app()->instance(SupportedExtensionsInterface::class, $extensions);
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
}
