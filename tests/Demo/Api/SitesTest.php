<?php namespace DemoTests\Api;

use \DemoTests\BaseTestCase;
use \Illuminate\Http\Response;

/**
 * Site tests.
 */
class SitesTest extends BaseTestCase
{
    /**
     * Test index and show sites.
     */
    public function testGetSites()
    {
        /** @var Response $response */
        $response = $this->callGet('/sites');
        $this->assertResponseOk();
        $this->assertNotEmpty($collection = json_decode($response->getContent()));
        foreach ($collection->data as $site) {
            $response = $this->callGet('/sites/' . $site->id);
            $this->assertResponseOk();
            $this->assertNotNull($item = json_decode($response->getContent()));
            $this->assertEquals($site->id, $item->data->id);
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
            "type" : "sites",
            "attributes" : {
              "name" : "Samples"
            }
          }
        }
EOT;
        // Create
        $response = $this->callPost('/sites', $requestBody);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertNotNull($site = json_decode($response->getContent())->data);
        $this->assertNotEmpty($site->id);
        $this->assertNotEmpty($response->headers->get('Location'));

        // re-read and check
        $this->assertNotNull($site = json_decode($this->callGet('/sites/' . $site->id)->getContent())->data);
        $this->assertEquals('Samples', $site->attributes->name);

        // Update
        $requestBody = "{
          \"data\" : {
            \"type\" : \"sites\",
            \"id\"   : \"$site->id\",
            \"attributes\" : {
              \"name\" : \"New name\"
            }
          }
        }";
        $response = $this->callPatch('/sites/' . $site->id, $requestBody);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        // re-read and check
        $this->assertNotNull($site = json_decode($this->callGet('/sites/' . $site->id)->getContent())->data);
        $this->assertEquals('New name', $site->attributes->name);

        // Delete
        $response = $this->callDelete('/sites/' . $site->id);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    /**
     * Test Eloquent-based models.
     */
    public function testEloquentRoute()
    {
        /** @var Response $response */
        $response = $this->callGet('/sites-eloquent/1');
        $this->assertResponseOk();
        $this->assertNotEmpty($response->getContent());
    }
}
