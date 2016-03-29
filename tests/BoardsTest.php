<?php namespace App\Tests;

use Neomerx\Tests\LimoncelloIlluminate\BoardsTestTrait as TestTrait;
use Neomerx\LimoncelloIlluminate\Schemas\BoardSchema as ModelSchema;

/**
 * @package App\Tests
 */
class BoardsTest extends TestCase
{
    use TestTrait;

    /** API sub-URL */
    const API_SUB_URL = ModelSchema::TYPE;
}
