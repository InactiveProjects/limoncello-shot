<?php namespace DemoTests;

use \TestCase;
use \Illuminate\Http\Response;

/**
 * Base test.
 */
class BaseTestCase extends TestCase
{
    /**
     * @param string $url
     *
     * @return Response
     */
    protected function callGet($url)
    {
        return $this->call('GET', $url, [], [], [], $this->getServerArray());
    }

    /**
     * @param string $url
     *
     * @return Response
     */
    protected function callDelete($url)
    {
        return $this->call('DELETE', $url, [], [], [], $this->getServerArray());
    }

    /**
     * @param string $url
     * @param string $content
     *
     * @return Response
     */
    protected function callPost($url, $content)
    {
        return $this->call('POST', $url, [], [], [], $this->getServerArray(), $content);
    }

    /**
     * @param string $url
     * @param string $content
     *
     * @return Response
     */
    protected function callPatch($url, $content)
    {
        return $this->call('PATCH', $url, [], [], [], $this->getServerArray(), $content);
    }

    /**
     * @return array
     */
    private function getServerArray()
    {
        $server  = [
            'CONTENT_TYPE' => 'application/vnd.api+json'
        ];
        $headers = [
            'CONTENT-TYPE' => 'application/vnd.api+json',
            'ACCEPT'       => 'application/vnd.api+json',
        ];
        foreach ($headers as $key => $value) {
            $server['HTTP_' . $key] = $value;
        }

        return $server;
    }
}
