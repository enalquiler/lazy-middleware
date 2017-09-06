<?php

declare(strict_types=1);

namespace Enalquiler\Middleware;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use PHPUnit\Framework\TestCase;
use function GuzzleHttp\Psr7\stream_for;

final class LazyMiddlewareTest extends TestCase
{
    /** @test */
    public function givenACallable_ItCreatesLazyMiddlewares(): void
    {
        $lazyMiddleware = lazy(function() {
            return new class implements MiddlewareInterface {
                public function process(ServerRequestInterface $request, DelegateInterface $delegate) {
                    return (new Response())->withBody(stream_for('Response!'));
                }
            };
        });
        
        assertInstanceOf(LazyMiddleware::class, $lazyMiddleware);
        
        $response = $lazyMiddleware->process(
            new ServerRequest('GET', '/'),
            new class implements DelegateInterface {
                public function process(ServerRequestInterface $request) {
                    return new Response();
                }
            }
        );
        
        $body = $response->getBody();
        $body->rewind();
        
        assertSame('Response!', $body->getContents());
    }
}
