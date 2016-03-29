<?php namespace App\Tests;

use Laravel\Lumen\Testing\TestCase as BaseTestCase;

/**
 * @package App\Tests
 */
class TestCase extends BaseTestCase
{
    /**
     * @inheritdoc
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }
}
