<?php namespace DemoTests\Api;

use \DemoTests\BaseTestCase;
use \Illuminate\Http\Response;

/**
 * Comments tests.
 */
class CommentsTest extends BaseTestCase
{
    /**
     * Test index and show comments.
     */
    public function testGetComments()
    {
        /** @var Response $response */
        $response = $this->callGet('/comments');
        $this->assertResponseOk();
        $this->assertNotEmpty($collection = json_decode($response->getContent()));
        foreach ($collection->data as $comment) {
            $response = $this->callGet('/comments/' . $comment->id);
            $this->assertResponseOk();
            $this->assertNotNull($item = json_decode($response->getContent()));
            $this->assertEquals($comment->id, $item->data->id);
        }
    }

    /**
     * Test store, update and delete.
     */
    public function testStoreUpdateAndDelete()
    {
        // assume post with id = 1 exists

        $requestBody = <<<EOT
        {
          "data" : {
            "type" : "comments",
            "attributes" : {
              "body" : "comment"
            },
            "links": {
              "author": {
                "linkage": { "type": "author", "id": "1" }
              }
            }
          }
        }
EOT;
        // Create
        $response = $this->callPost('/comments', $requestBody);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertNotNull($comment = json_decode($response->getContent())->data);
        $this->assertNotEmpty($comment->id);
        $this->assertNotEmpty($response->headers->get('Location'));

        // re-read and check
        $this->assertNotNull($comment = json_decode($this->callGet('/comments/' . $comment->id)->getContent())->data);
        $this->assertEquals('comment', $comment->attributes->body);

        // Update
        $requestBody = "{
          \"data\" : {
            \"type\" : \"comments\",
            \"id\"   : \"$comment->id\",
            \"attributes\" : {
              \"body\" : \"new comment\"
            }
          }
        }";
        $response = $this->callPatch('/comments/' . $comment->id, $requestBody);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        // re-read and check
        $this->assertNotNull($comment = json_decode($this->callGet('/comments/' . $comment->id)->getContent())->data);
        $this->assertEquals('new comment', $comment->attributes->body);

        // Delete
        $response = $this->callDelete('/comments/' . $comment->id);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}
