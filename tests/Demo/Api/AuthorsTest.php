<?php namespace DemoTests\Api;

use \DemoTests\BaseTestCase;
use \Illuminate\Http\Response;

class AuthorsTest extends BaseTestCase
{
    /** API URL */
    const API_URL = 'api/v1/authors/';

    /**
     * Test index and show authors.
     */
    public function testGetAuthors()
    {
        /** @var Response $response */
        $response = $this->callGet(self::API_URL);
        $this->assertResponseOk();
        $this->assertNotEmpty($collection = json_decode($response->getContent()));
        foreach ($collection->data as $author) {
            $response = $this->callGet(self::API_URL . $author->id);
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
              "first"   : "John",
              "last"    : "Dow",
              "twitter" : "@johnDow"
            }
          }
        }
EOT;
        // Create
        $response = $this->callPost(self::API_URL, $requestBody);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertNotNull($author = json_decode($response->getContent())->data);
        $this->assertNotEmpty($author->id);
        $this->assertNotEmpty($response->headers->get('Location'));

        // re-read and check
        $this->assertNotNull($author = json_decode($this->callGet(self::API_URL . $author->id)->getContent())->data);
        $this->assertEquals('John', $author->attributes->first);

        // Update
        $requestBody = "{
          \"data\" : {
            \"type\" : \"authors\",
            \"id\"   : \"$author->id\",
            \"attributes\" : {
              \"first\" : \"Jane\"
            }
          }
        }";
        $response = $this->callPatch(self::API_URL . $author->id, $requestBody);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        // re-read and check
        $this->assertNotNull($author = json_decode($this->callGet(self::API_URL . $author->id)->getContent())->data);
        $this->assertEquals('Jane', $author->attributes->first);

        // Delete
        $response = $this->callDelete(self::API_URL . $author->id);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}
