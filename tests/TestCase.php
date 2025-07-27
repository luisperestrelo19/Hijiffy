<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Additional setup can be done here if needed
    }

    /**
     * Tear down the test environment.
     */
    protected function tearDown(): void
    {
        // Clean up after tests if necessary

        parent::tearDown();
    }
}
