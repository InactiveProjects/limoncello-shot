<?php namespace App\Http\Middleware;

use \Log;
use \Neomerx\Cors\Contracts\AnalysisResultInterface;
use \Neomerx\CorsIlluminate\CorsMiddleware as BaseCorsMiddleware;

/**
 * @package Neomerx\LimoncelloShot
 */
class CorsMiddleware extends BaseCorsMiddleware
{
    /**
     * @inheritdoc
     */
    protected function getResponseOnError(AnalysisResultInterface $analysisResult)
    {
        switch ($analysisResult->getRequestType()) {
            case AnalysisResultInterface::ERR_NO_HOST_HEADER:
                Log::debug('CORS error: no Host header in request');
                break;
            case AnalysisResultInterface::ERR_ORIGIN_NOT_ALLOWED:
                Log::debug('CORS error: request Origin is not allowed');
                break;
            case AnalysisResultInterface::ERR_METHOD_NOT_SUPPORTED:
                Log::debug('CORS error: request method is not allowed');
                break;
            case AnalysisResultInterface::ERR_HEADERS_NOT_SUPPORTED:
                Log::debug('CORS error: one or more request headers are not allowed');
                break;
        }

        return parent::getResponseOnError($analysisResult);
    }
}
