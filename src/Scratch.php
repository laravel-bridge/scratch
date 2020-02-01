<?php

namespace LaravelBridge\Scratch;

use Illuminate\Container\Container as LaravelContainer;

class Scratch extends LaravelContainer
{
    public function alwaysTrue()
    {
        return true;
    }
}
