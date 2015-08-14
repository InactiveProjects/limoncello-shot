<?php namespace DemoTests\Errors;

use \App\Exceptions\Handler;
use \DemoTests\BaseTestCase;
use \Illuminate\Http\Request;
use \Illuminate\Http\Response;
use \Neomerx\JsonApi\Contracts\Document\DocumentInterface;
use \Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class ExceptionHandlerTest extends BaseTestCase
{
    /** API URL prefix */
    const API_URL_PREFIX = 'api/v1/';

    /**
     * Test 404 error.
     */
    public function test404ForUrls()
    {
        /** @var Response $response */
        $response = $this->callGet('/not-existing-path');
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEmpty($response->getContent());
    }

    /**
     * Test 404 error. It actually tests custom error rendering which is set up for 'model not found' exceptions.
     */
    public function test404ForModels()
    {
        /** @var Response $response */
        $response = $this->callGet(self::API_URL_PREFIX . 'sites/999999999999');
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertNotEmpty($response->getContent());
    }

    /**
     * Test supported extensions are added to response.
     */
    public function testSupportedExtensionsPresent()
    {
        // send invalid name
        $requestBody = <<<EOT
        {
          "data" : {
            "type" : "authors",
            "attributes" : {
              "first_name" : null
            }
          }
        }
EOT;
        // Create
        $response = $this->callPost(self::API_URL_PREFIX . 'authors', $requestBody);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(
            'application/vnd.api+json;supported-ext="ext1,ex3"',
            $response->headers->get('Content-Type')
        );

        $this->assertNotEmpty(json_decode($response->getContent())->errors);
    }

    /**
     * Test render for TooManyRequestsHttpException.
     */
    public function testTooManyRequestsRender()
    {
        // Preparation

        /** @var Handler $handler */
        $this->assertNotNull($handler = app(Handler::class));

        $retryAfterSeconds = 12;
        $message = 'Hold On For a Second';
        $exception = new TooManyRequestsHttpException($retryAfterSeconds, $message);
        $request   = new Request();

        // Test
        $this->assertNotNull($response = $handler->render($request, $exception));

        // Check
        $errors = json_decode($response->getContent())->{DocumentInterface::KEYWORD_ERRORS};
        $this->assertCount(1, $errors);
        $this->assertNotNull($error = $errors[0]);
        $this->assertEquals($message, $error->{DocumentInterface::KEYWORD_ERRORS_DETAIL});

        $this->assertEquals($retryAfterSeconds, $response->headers->get('Retry-After'));
    }
}
