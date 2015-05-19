<?php namespace DemoTests\Api;

use \DemoTests\BaseTestCase;
use \Illuminate\Http\Response;

/**
 * Post tests.
 */
class PostsTest extends BaseTestCase
{
    /**
     * Test index and show posts.
     */
    public function testGetPosts()
    {
        /** @var Response $response */
        $response = $this->callGet('/posts');
        $this->assertResponseOk();
        $this->assertNotEmpty($collection = json_decode($response->getContent()));
        foreach ($collection->data as $post) {
            $response = $this->callGet('/posts/' . $post->id);
            $this->assertResponseOk();
            $this->assertNotNull($item = json_decode($response->getContent()));
            $this->assertEquals($post->id, $item->data->id);
        }
    }

    /**
     * Test store, update and delete.
     */
    public function testStoreUpdateAndDelete()
    {
        // assume author and site with ids = 1 exist

        $requestBody = <<<EOT
        {
          "data" : {
            "type" : "posts",
            "attributes" : {
              "title" : "post title",
              "body"  : "post body"
            },
            "links": {
              "author": {
                "linkage": { "type": "author", "id": "1" }
              },
              "site": {
                "linkage": { "type": "sites", "id": "1" }
              }
            }
          }
        }
EOT;
        // Create
        $response = $this->callPost('/posts', $requestBody);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertNotNull($post = json_decode($response->getContent())->data);
        $this->assertNotEmpty($post->id);
        $this->assertNotEmpty($response->headers->get('Location'));

        // re-read and check
        $this->assertNotNull($post = json_decode($this->callGet('/posts/' . $post->id)->getContent())->data);
        $this->assertEquals('post body', $post->attributes->body);

        // Update
        $requestBody = "{
          \"data\" : {
            \"type\" : \"posts\",
            \"id\"   : \"$post->id\",
            \"attributes\" : {
              \"body\" : \"new body\"
            }
          }
        }";
        $response = $this->callPatch('/posts/' . $post->id, $requestBody);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        // re-read and check
        $this->assertNotNull($post = json_decode($this->callGet('/posts/' . $post->id)->getContent())->data);
        $this->assertEquals('new body', $post->attributes->body);

        // Delete
        $response = $this->callDelete('/posts/' . $post->id);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}
