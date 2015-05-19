<?php namespace DemoTests\Errors;

use \DemoTests\BaseTestCase;
use \Illuminate\Http\Response;

/**
 * Exceptions handler
 */
class ExceptionHandlerTest extends BaseTestCase
{
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
        $response = $this->callGet('/sites/999999999999');
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
        $response = $this->callPost('/authors', $requestBody);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(
            'application/vnd.api+json;supported-ext="ext1,ex3"',
            $response->headers->get('Content-Type')
        );
    }
}
