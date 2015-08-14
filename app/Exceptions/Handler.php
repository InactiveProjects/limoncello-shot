<?php namespace App\Exceptions;

use \Illuminate\Http\Request;
use \Illuminate\Http\Response;
use \Neomerx\JsonApi\Document\Error;
use \Neomerx\Limoncello\Errors\RendererContainer;
use \Neomerx\JsonApi\Parameters\Headers\MediaType;
use \App\Http\Controllers\JsonApi\LumenIntegration;
use \Neomerx\Cors\Contracts\AnalysisResultInterface;
use \Neomerx\Limoncello\Contracts\IntegrationInterface;
use \Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use \Neomerx\JsonApi\Contracts\Exceptions\RendererInterface;
use \Neomerx\JsonApi\Contracts\Exceptions\RendererContainerInterface;
use \Neomerx\JsonApi\Contracts\Parameters\SupportedExtensionsInterface;

use \Exception;
use \UnexpectedValueException;

use \Firebase\JWT\ExpiredException;
use \Firebase\JWT\SignatureInvalidException;

use \Illuminate\Contracts\Validation\ValidationException;
use \Illuminate\Database\Eloquent\ModelNotFoundException;
use \Illuminate\Database\Eloquent\MassAssignmentException;

use \Symfony\Component\HttpKernel\Exception\GoneHttpException;
use \Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use \Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use \Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use \Symfony\Component\HttpKernel\Exception\LengthRequiredHttpException;
use \Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use \Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use \Symfony\Component\HttpKernel\Exception\PreconditionRequiredHttpException;
use \Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

/**
 * @package Neomerx\LimoncelloShot
 */
class Handler extends ExceptionHandler
{
    /**
     * @var RendererContainerInterface
     */
    private $renderContainer;

    /**
     * @var IntegrationInterface
     */
    private $integration;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->integration     = new LumenIntegration();
        $this->renderContainer = new RendererContainer($this->integration);

        $this->registerCustomExceptions();
    }

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        ExpiredException::class,
        GoneHttpException::class,
        ValidationException::class,
        ConflictHttpException::class,
        NotFoundHttpException::class,
        ModelNotFoundException::class,
        BadRequestHttpException::class,
        UnexpectedValueException::class,
        AccessDeniedHttpException::class,
        SignatureInvalidException::class,
        UnauthorizedHttpException::class,
        NotAcceptableHttpException::class,
        LengthRequiredHttpException::class,
        TooManyRequestsHttpException::class,
        MethodNotAllowedHttpException::class,
        PreconditionFailedHttpException::class,
        PreconditionRequiredHttpException::class,
        UnsupportedMediaTypeHttpException::class,
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request   $request
     * @param Exception $exception
     *
     * @return Response
     */
    public function render($request, Exception $exception)
    {
        $request ?: null;

        $render = $this->renderContainer->getRenderer(get_class($exception));

        if (($supportedExtensions = $this->getSupportedExtensions()) !== null) {
            $render->withSupportedExtensions($supportedExtensions);
        }

        $corsHeaders = $this->mergeCorsHeadersTo();
        $mediaType   = new MediaType(MediaType::JSON_API_TYPE, MediaType::JSON_API_SUB_TYPE);

        return $render->withHeaders($corsHeaders)->withMediaType($mediaType)->render($exception);
    }

    /**
     * Here you can add 'exception -> HTTP code' mapping or custom exception renders.
     */
    private function registerCustomExceptions()
    {
        $this->renderContainer->registerHttpCodeMapping([

            MassAssignmentException::class   => Response::HTTP_FORBIDDEN,
            ExpiredException::class          => Response::HTTP_UNAUTHORIZED,
            SignatureInvalidException::class => Response::HTTP_UNAUTHORIZED,
            UnexpectedValueException::class  => Response::HTTP_BAD_REQUEST,

        ]);

        //
        // That's an example of how to create custom response with JSON API Error.
        //
        $custom404render = $this->getCustom404Render();

        // Another example how Eloquent ValidationException could be used.
        // You can use validation as simple as this
        //
        // /** @var \Illuminate\Validation\Validator $validator */
        // if ($validator->fails()) {
        //     throw new ValidationException($validator);
        // }
        //
        // and it will return JSON-API error(s) from your API service
        $customValidationRender = $this->getCustomValidationRender();

        // This render is interesting because it takes HTTP Headers from exception and
        // adds them to HTTP Response (via render parameter $headers)
        $customTooManyRequestsRender = $this->getCustomTooManyRequestsRender();

        $this->renderContainer->registerRenderer(ModelNotFoundException::class, $custom404render);
        $this->renderContainer->registerRenderer(ValidationException::class, $customValidationRender);
        $this->renderContainer->registerRenderer(TooManyRequestsHttpException::class, $customTooManyRequestsRender);
    }

    /**
     * @return RendererInterface
     */
    private function getCustom404Render()
    {
        $converter = function (ModelNotFoundException $exception) {
            $exception ?: null;

            // Prepare Error object (e.g. take info from the exception)
            $title = 'Requested item not found';
            $error = new Error(null, null, null, null, $title);

            return $error;
        };

        $renderer = $this->renderContainer->createConvertContentRenderer(Response::HTTP_NOT_FOUND, $converter);

        return $renderer;
    }

    /**
     * @return RendererInterface
     */
    private function getCustomValidationRender()
    {
        $converter = function (ValidationException $exception) {
            // Prepare Error object (e.g. take info from the exception)
            $title  = 'Validation fails';

            $errors = [];
            foreach ($exception->errors()->all() as $validationMessage) {
                $errors[] = new Error(null, null, null, null, $title, $validationMessage);
            }

            return $errors;
        };

        $renderer = $this->renderContainer->createConvertContentRenderer(Response::HTTP_BAD_REQUEST, $converter);

        return $renderer;
    }

    /**
     * @return RendererInterface
     */
    private function getCustomTooManyRequestsRender()
    {
        /** @var RendererInterface $renderer */
        $renderer  = null;
        $converter = function (TooManyRequestsHttpException $exception) use (&$renderer) {
            // Prepare Error object (e.g. take info from the exception)
            $title   = 'Validation fails';
            $message = $exception->getMessage();
            $error   = new Error(null, null, null, null, $title, $message);

            $headers = $exception->getHeaders();
            $renderer->withHeaders($headers);

            return $error;
        };

        $renderer = $this->renderContainer->createConvertContentRenderer(Response::HTTP_TOO_MANY_REQUESTS, $converter);

        return $renderer;
    }

    /**
     * @param array $headers
     *
     * @return array
     */
    private function mergeCorsHeadersTo(array $headers = [])
    {
        $resultHeaders = $headers;
        if (app()->resolved(AnalysisResultInterface::class) === true) {
            /** @var AnalysisResultInterface|null $result */
            $result = app(AnalysisResultInterface::class);
            if ($result !== null) {
                $resultHeaders = array_merge($headers, $result->getResponseHeaders());
            }
        }

        return $resultHeaders;
    }

    /**
     * @return SupportedExtensionsInterface|null
     */
    private function getSupportedExtensions()
    {
        /** @var SupportedExtensionsInterface|null $supportedExtensions */
        $supportedExtensions = app()->resolved(SupportedExtensionsInterface::class) === false ? null :
            app()->make(SupportedExtensionsInterface::class);

        return $supportedExtensions;
    }
}
