<?php namespace DemoTests\Jwt;

use \App\Models\User;
use \DemoTests\BaseTestCase;
use \App\Jwt\UserJwtCodecInterface;

class UserJwtCodecTest extends BaseTestCase
{
    /**
     * @var UserJwtCodecInterface
     */
    private $codec;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->assertNotNull($this->codec = app(UserJwtCodecInterface::class));
    }

    /**
     * Test User encode to JWT and decode from JWT back.
     */
    public function testUserEncodeDecode()
    {
        $users = User::all();
        $this->assertGreaterThan(0, count($users));

        /** @var User $firstUser */
        $this->assertNotNull($firstUser = $users[0]);
        unset($users);

        $this->assertNotNull($jwt = $this->codec->encode($firstUser));
        $this->assertNotNull($sameUser = $this->codec->decode($jwt));

        $this->assertEquals($firstUser->getAuthIdentifier(), $sameUser->getAuthIdentifier());
    }
}
