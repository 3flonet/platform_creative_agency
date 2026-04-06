<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // 🛡️ Force installed state for tests
        if (!file_exists(storage_path('installed.lock'))) {
            if (!file_exists(storage_path())) {
                mkdir(storage_path(), 0777, true);
            }
            touch(storage_path('installed.lock'));
        }
    }
}
