<?php namespace DemoTests\Api;

use \DemoTests\BaseTestCase;
use \Illuminate\Http\Response;

class UsersTest extends BaseTestCase
{
    /** API URL */
    const API_URL = 'api/v1/users/';

    /**
     * Test index and show users.
     */
    public function testGetUsers()
    {
        /** @var Response $response */
        $response = $this->callGet(self::API_URL);
        $this->assertResponseOk();
        $this->assertNotEmpty($collection = json_decode($response->getContent()));
        foreach ($collection->data as $user) {
            $response = $this->callGet(self::API_URL . $user->id);
            $this->assertResponseOk();
            $this->assertNotNull($item = json_decode($response->getContent()));
            $this->assertEquals($user->id, $item->data->id);
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
            "type" : "users",
            "attributes" : {
              "name"     : "John Dow",
              "email"    : "john.dow@mail.foo",
              "password" : "secret"
            }
          }
        }
EOT;
        // Create
        $response = $this->callPost(self::API_URL, $requestBody);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertNotNull($user = json_decode($response->getContent())->data);
        $this->assertNotEmpty($user->id);
        $this->assertNotEmpty($response->headers->get('Location'));

        // re-read and check
        $this->assertNotNull($user = json_decode($this->callGet(self::API_URL . $user->id)->getContent())->data);
        $this->assertEquals('John Dow', $user->attributes->name);

        // Update
        $requestBody = "{
          \"data\" : {
            \"type\" : \"users\",
            \"id\"   : \"$user->id\",
            \"attributes\" : {
              \"name\" : \"Jane\"
            }
          }
        }";
        $response = $this->callPatch(self::API_URL . $user->id, $requestBody);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        // re-read and check
        $this->assertNotNull($user = json_decode($this->callGet(self::API_URL . $user->id)->getContent())->data);
        $this->assertEquals('Jane', $user->attributes->name);

        // Delete
        $response = $this->callDelete(self::API_URL . $user->id);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}
