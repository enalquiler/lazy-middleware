<?php

namespace Enalquiler\Middleware;

function lazy(callable $factory)
{
    return new LazyMiddleware($factory);
}