<?php namespace App\Tests;

//use Laravel\Lumen\Testing\DatabaseTransactions;

/**
 * @package App\Tests
 */
class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->get('/');

//        $this->assertEquals(
//            $this->app->version(), $this->response->getContent()
//        );
        $this->assertEquals('JSON API Neomerx Demo Application', $this->response->getContent());
    }
}
