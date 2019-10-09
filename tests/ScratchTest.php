<?php

namespace Tests;

use LaravelBridge\Scratch\Scratch;
use PHPUnit\Framework\TestCase;

class ScratchTest extends TestCase
{
    public function testSample()
    {
        $this->assertTrue((new Scratch())->alwaysTrue());
    }
}
