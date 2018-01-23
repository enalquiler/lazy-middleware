<?php

declare(strict_types=1);

namespace Enalquiler\Middleware;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PHPUnit\Framework\TestCase;
use function GuzzleHttp\Psr7\stream_for;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class LazyMiddlewareTest extends TestCase
{
    /** @test */
    public function givenACallable_ItCreatesLazyMiddlewares(): void
    {
        $lazyMiddleware = lazy(function() {
            return new class implements MiddlewareInterface {
                public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
                    return (new Response())->withBody(stream_for('Response!'));
                }
            };
        });
        
        assertInstanceOf(LazyMiddleware::class, $lazyMiddleware);
        
        $response = $lazyMiddleware->process(
            new ServerRequest('GET', '/'),
            new class implements RequestHandlerInterface {
                public function handle(ServerRequestInterface $request): ResponseInterface {
                    return new Response();
                }
            }
        );
        
        $body = $response->getBody();
        $body->rewind();
        
        assertSame('Response!', $body->getContents());
    }
}
