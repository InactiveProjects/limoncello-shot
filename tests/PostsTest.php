<?php namespace App\Tests;

use Neomerx\Tests\LimoncelloIlluminate\PostsTestTrait as TestTrait;
use Neomerx\LimoncelloIlluminate\Schemas\PostSchema as ModelSchema;

/**
 * @package App\Tests
 */
class PostsTest extends TestCase
{
    use TestTrait;

    /** API sub-URL */
    const API_SUB_URL = ModelSchema::TYPE;
}
