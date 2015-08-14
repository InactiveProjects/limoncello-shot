<?php namespace DemoTests\Api;

use \DemoTests\BaseTestCase;
use \Illuminate\Http\Response;
use \Neomerx\JsonApi\Contracts\Parameters\ParametersParserInterface;

class CommentsTest extends BaseTestCase
{
    /** API URL */
    const API_URL = 'api/v1/comments/';

    /**
     * Test index and show comments.
     */
    public function testGetComments()
    {
        $filter = [
            'ids' => ['1', '2'],
        ];

        $parameters = [
            ParametersParserInterface::PARAM_FILTER => $filter,
        ];

        /** @var Response $response */
        $response = $this->callGet(self::API_URL, $parameters);
        $this->assertResponseOk();
        $this->assertNotEmpty($collection = json_decode($response->getContent()));
        foreach ($collection->data as $comment) {
            $response = $this->callGet(self::API_URL . $comment->id);
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
            "relationships": {
              "post": {
                "data": { "type": "post", "id": "1" }
              }
            }
          }
        }
EOT;
        // Create
        $response = $this->callPost(self::API_URL, $requestBody);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertNotNull($comment = json_decode($response->getContent())->data);
        $this->assertNotEmpty($comment->id);
        $this->assertNotEmpty($response->headers->get('Location'));

        // re-read and check
        $this->assertNotNull($comment = json_decode($this->callGet(self::API_URL . $comment->id)->getContent())->data);
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
        $response = $this->callPatch(self::API_URL . $comment->id, $requestBody);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        // re-read and check
        $this->assertNotNull($comment = json_decode($this->callGet(self::API_URL . $comment->id)->getContent())->data);
        $this->assertEquals('new comment', $comment->attributes->body);

        // Delete
        $response = $this->callDelete(self::API_URL . $comment->id);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}
