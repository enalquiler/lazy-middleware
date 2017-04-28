<?php

namespace Enalquiler\MiddleWare;

function lazy(callable $factory)
{
    return new LazyMiddleware($factory);
}