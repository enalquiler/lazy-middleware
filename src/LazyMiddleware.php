<?php

namespace Enalquiler\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LazyMiddleware implements MiddlewareInterface
{
    /**
     * @var callable
     */
    private $factory;

    /**
     * @var MiddlewareInterface
     */
    private $app;

    public function __construct(callable $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->createApp()->process($request, $handler);
    }

    /**
     * @return MiddlewareInterface
     */
    private function createApp()
    {
        $this->app = $this->app ?: call_user_func($this->factory);

        return $this->app;
    }
}