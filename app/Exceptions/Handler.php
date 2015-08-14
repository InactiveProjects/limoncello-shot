<?php namespace App\Exceptions;

use \Closure;
use \Illuminate\Http\Request;
use \Illuminate\Http\Response;
use \Neomerx\JsonApi\Document\Error;
use \Neomerx\JsonApi\Factories\Factory;
use \Neomerx\Limoncello\Config\Config as C;
use \Neomerx\JsonApi\Encoder\EncoderOptions;
use \Neomerx\Limoncello\Errors\RenderContainer;
use \App\Http\Controllers\JsonApi\LumenIntegration;
use \Neomerx\Cors\Contracts\AnalysisResultInterface;
use \Neomerx\Limoncello\Contracts\IntegrationInterface;
use \Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use \Neomerx\JsonApi\Contracts\Exceptions\RenderContainerInterface;
use \Neomerx\JsonApi\Contracts\Parameters\SupportedExtensionsInterface;

use \Exception;
use \UnexpectedValueException;

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
     * @var RenderContainerInterface
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
        $this->integration = new LumenIntegration();

        $extensionsClosure = function () {
            /** @var SupportedExtensionsInterface $supportedExtensions */
            $supportedExtensions = app()->resolved(SupportedExtensionsInterface::class) === false ? null :
                app()->make(SupportedExtensionsInterface::class);
            return $supportedExtensions;
        };

        $this->renderContainer = new RenderContainer(new Factory(), $this->integration, $extensionsClosure);

        $this->registerCustomExceptions();
    }

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        GoneHttpException::class,
        ValidationException::class,
        ConflictHttpException::class,
        NotFoundHttpException::class,
        ModelNotFoundException::class,
        BadRequestHttpException::class,
        UnexpectedValueException::class,
        AccessDeniedHttpException::class,
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
        $render = $this->renderContainer->getRender($exception);
        return $render($request, $exception);
    }

    /**
     * Here you can add 'exception -> HTTP code' mapping or custom exception renders.
     */
    private function registerCustomExceptions()
    {
        $this->renderContainer->registerHttpCodeMapping([

            MassAssignmentException::class  => Response::HTTP_FORBIDDEN,
            UnexpectedValueException::class => Response::HTTP_BAD_REQUEST,

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

        $this->renderContainer->registerRender(ModelNotFoundException::class, $custom404render);
        $this->renderContainer->registerRender(ValidationException::class, $customValidationRender);
        $this->renderContainer->registerRender(TooManyRequestsHttpException::class, $customTooManyRequestsRender);
    }

    /**
     * @return Closure
     */
    private function getCustom404Render()
    {
        $custom404render = function (/*Request $request, ModelNotFoundException $exception*/) {
            // This render can convert JSON API Error to Response
            $jsonApiErrorRender = $this->renderContainer->getErrorsRender(Response::HTTP_NOT_FOUND);

            // Prepare Error object (e.g. take info from the exception)
            $title = 'Requested item not found';
            $error = new Error(null, null, null, null, $title);

            // Convert error (note it accepts array of errors) to HTTP response
            return $jsonApiErrorRender([$error], $this->getEncoderOptions(), $this->mergeCorsHeadersTo());
        };

        return $custom404render;
    }

    /**
     * @return Closure
     */
    private function getCustomValidationRender()
    {
        $customValidationRender = function (Request $request, ValidationException $exception) {
            $request ?: null; // avoid 'unused' warning

            // This render can convert JSON API Error to Response
            $jsonApiErrorRender = $this->renderContainer->getErrorsRender(Response::HTTP_BAD_REQUEST);

            // Prepare Error object (e.g. take info from the exception)
            $title  = 'Validation fails';
            $errors = [];
            foreach ($exception->errors()->all() as $validationMessage) {
                $errors[] = new Error(null, null, null, null, $title, $validationMessage);
            }

            // Convert error (note it accepts array of errors) to HTTP response
            return $jsonApiErrorRender($errors, $this->getEncoderOptions(), $this->mergeCorsHeadersTo());
        };

        return $customValidationRender;
    }

    /**
     * @return Closure
     */
    private function getCustomTooManyRequestsRender()
    {
        $customTooManyRequestsRender = function (Request $request, TooManyRequestsHttpException $exception) {
            $request ?: null; // avoid 'unused' warning

            // This render can convert JSON API Error to Response
            $jsonApiErrorRender = $this->renderContainer->getErrorsRender(Response::HTTP_TOO_MANY_REQUESTS);

            // Prepare Error object (e.g. take info from the exception)
            $title   = 'Validation fails';
            $message = $exception->getMessage();
            $headers = $exception->getHeaders();
            $error   = new Error(null, null, null, null, $title, $message);

            // Convert error (note it accepts array of errors) to HTTP response
            return $jsonApiErrorRender([$error], $this->getEncoderOptions(), $this->mergeCorsHeadersTo($headers));
        };

        return $customTooManyRequestsRender;
    }

    /**
     * @return EncoderOptions
     */
    private function getEncoderOptions()
    {
        // Load JSON formatting options from config
        $options = array_get(
            $this->integration->getConfig(),
            C::JSON . '.' . C::JSON_OPTIONS,
            C::JSON_OPTIONS_DEFAULT
        );
        $encodeOptions = new EncoderOptions($options);

        return $encodeOptions;
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
}
