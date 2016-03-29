<?php namespace App\Tests;

use Neomerx\Tests\LimoncelloIlluminate\RolesTestTrait as TestTrait;
use Neomerx\LimoncelloIlluminate\Schemas\RoleSchema as ModelSchema;

/**
 * @package App\Tests
 */
class RolesTest extends TestCase
{
    use TestTrait;

    /** API sub-URL */
    const API_SUB_URL = ModelSchema::TYPE;
}
