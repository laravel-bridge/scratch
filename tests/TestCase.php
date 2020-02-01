<?php

namespace Tests;

use Mockery;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    protected function resourcePath(string $path = ''): string
    {
        $defaultPath = __DIR__ . DIRECTORY_SEPARATOR . 'Fixture' . DIRECTORY_SEPARATOR . 'resources';

        return $defaultPath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    protected function storagePath(string $path = ''): string
    {
        $defaultPath = __DIR__ . DIRECTORY_SEPARATOR . 'Fixture' . DIRECTORY_SEPARATOR . 'storage';

        return $defaultPath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}
