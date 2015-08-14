<?php namespace DemoTests;

use \TestCase;
use \App\Models\User;
use \UsersTableSeeder;
use \Illuminate\Http\Response;
use \App\Jwt\UserJwtCodecInterface;

class BaseTestCase extends TestCase
{
    /**
     * @param string $url
     * @param array  $parameters
     *
     * @return Response
     */
    protected function callGet($url, array $parameters = [])
    {
        return $this->call('GET', $url, $parameters, [], [], $this->getServerArray());
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

        // required for csrf_token()
        \Session::start();

        $basicAuth = $this->getBasicAuthHeader();
        $this->assertNotEmpty($basicAuth);

        $jwtAuth = $this->getJwtAuthHeader();
        $this->assertNotEmpty($jwtAuth);

        // Here you can choose what auth will be used for testing (basic or jwt)
        $auth = $jwtAuth;
        $headers = [
            'CONTENT-TYPE'     => 'application/vnd.api+json',
            'ACCEPT'           => 'application/vnd.api+json',
            'Authorization'    => $auth,
            'X-Requested-With' => 'XMLHttpRequest',
            'X-CSRF-TOKEN'     => csrf_token(),
        ];
        foreach ($headers as $key => $value) {
            $server['HTTP_' . $key] = $value;
        }

        return $server;
    }

    /**
     * @return string
     */
    private function getBasicAuthHeader()
    {
        return 'Basic ' . base64_encode(UsersTableSeeder::SAMPLE_LOGIN . ':' . UsersTableSeeder::SAMPLE_PASSWORD);
    }

    private function getJwtAuthHeader()
    {
        $allUsers = User::all();
        $this->assertGreaterThan(0, count($allUsers));
        /** @var User $firstUser */
        $this->assertNotNull($firstUser = $allUsers[0]);

        /** @var UserJwtCodecInterface $jwtCodec */
        $this->assertNotNull($jwtCodec = app(UserJwtCodecInterface::class));
        $jwt = $jwtCodec->encode($firstUser);

        return 'Bearer ' . $jwt;
    }
}
