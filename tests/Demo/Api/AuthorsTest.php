<?php namespace DemoTests\Api;

use \DemoTests\BaseTestCase;
use \Illuminate\Http\Response;

/**
 * Author tests.
 */
class AuthorsTest extends BaseTestCase
{
    /**
     * Test index and show authors.
     */
    public function testGetAuthors()
    {
        /** @var Response $response */
        $response = $this->callGet('/authors');
        $this->assertResponseOk();
        $this->assertNotEmpty($collection = json_decode($response->getContent()));
        foreach ($collection->data as $author) {
            $response = $this->callGet('/authors/' . $author->id);
            $this->assertResponseOk();
            $this->assertNotNull($item = json_decode($response->getContent()));
            $this->assertEquals($author->id, $item->data->id);
        }
    }

    /**
     * Test store, update and delete.
     */
    public function testStoreUpdateAndDelete()
    {
        $requestBody = <<<EOT
        {
          "data" : {
            "type" : "authors",
            "attributes" : {
              "first_name" : "John",
              "last_name"  : "Dow",
              "twitter"    : "@johnDow"
            }
          }
        }
EOT;
        // Create
        $response = $this->callPost('/authors', $requestBody);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertNotNull($author = json_decode($response->getContent())->data);
        $this->assertNotEmpty($author->id);
        $this->assertNotEmpty($response->headers->get('Location'));

        // re-read and check
        $this->assertNotNull($author = json_decode($this->callGet('/authors/' . $author->id)->getContent())->data);
        $this->assertEquals('John', $author->attributes->first_name);

        // Update
        $requestBody = "{
          \"data\" : {
            \"type\" : \"authors\",
            \"id\"   : \"$author->id\",
            \"attributes\" : {
              \"first_name\" : \"Jane\"
            }
          }
        }";
        $response = $this->callPatch('/authors/' . $author->id, $requestBody);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        // re-read and check
        $this->assertNotNull($author = json_decode($this->callGet('/authors/' . $author->id)->getContent())->data);
        $this->assertEquals('Jane', $author->attributes->first_name);

        // Delete
        $response = $this->callDelete('/authors/' . $author->id);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}
