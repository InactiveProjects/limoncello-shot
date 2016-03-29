<?php namespace App\Tests;

use Neomerx\Tests\LimoncelloIlluminate\CommentsTestTrait as TestTrait;
use Neomerx\LimoncelloIlluminate\Schemas\CommentSchema as ModelSchema;

/**
 * @package App\Tests
 */
class CommentsTest extends TestCase
{
    use TestTrait;

    /** API sub-URL */
    const API_SUB_URL = ModelSchema::TYPE;
}
