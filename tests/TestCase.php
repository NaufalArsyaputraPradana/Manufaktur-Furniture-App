<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        $compiledRoot = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'fms-views-tests';
        $compiledPath = $compiledRoot . DIRECTORY_SEPARATOR . uniqid('views_', true);
        if (!is_dir($compiledPath)) {
            mkdir($compiledPath, 0755, true);
        }

        config(['view.compiled' => $compiledPath]);
    }
}
