<?php namespace App\Tests;

use Neomerx\Tests\LimoncelloIlluminate\UsersTestTrait as TestTrait;
use Neomerx\LimoncelloIlluminate\Schemas\UserSchema as ModelSchema;

/**
 * @package App\Tests
 */
class UsersTest extends TestCase
{
    use TestTrait;

    /** API sub-URL */
    const API_SUB_URL = ModelSchema::TYPE;
}
