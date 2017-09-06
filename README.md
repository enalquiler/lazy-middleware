# A Lazy PSR-15 Middleware

A dead-simple lazy PSR-15 middleware.

## Installation

```
composer require enalquiler/lazy-middleware
```

## Usage

### With [Zend Stratigility](https://zendframework.github.io/zend-stratigility/middleware/)

Zend Stratigility 

```php
<?php

use Zend\Diactoros\Response;
use Zend\Diactoros\Server;
use Zend\Stratigility\MiddlewarePipe;
use Zend\Stratigility\NoopFinalHandler;
use Enalquiler\Middleware\SymfonyMiddleware;
use function Enalquiler\Middleware\lazy;

require __DIR__ . '/../vendor/autoload.php';

$app = new MiddlewarePipe();
$app->setResponsePrototype(new Response());

$server = Server::createServer($app, $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);

$app
    ->pipe('/foo', lazy(function() {
        // Run heavy computations
        return new HardToBuildMiddleware();
    }))
;

$server->listen(new NoopFinalHandler());
```

### With [Middleman](https://github.com/mindplay-dk/middleman)

```php
<?php

use Psr\Http\Message\RequestInterface as Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;
use mindplay\middleman\Dispatcher;
use Enalquiler\Middleware\lazy;

$dispatcher = new Dispatcher([
    lazy(function() {
        // Run heavy computations
        return new HardToBuildMiddleware();
    }),
    function (Request $request) {
        return (new Response())->withBody(...); // abort middleware stack and return the response
    },
    // ...
]);

$response = $dispatcher->dispatch(new ServerRequest($_SERVER, $_FILES));
```

## Running the tests

```
php vendor/bin/phpunit
```

## Authors

* **David Martínez** - *Initial work*
* **Christian Soronellas**
* **Enalquiler Engineering**

See also the list of [contributors](https://github.com/your/project/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Acknowledgments

* This middleware was inspired by the work of **https://github.com/stackphp/LazyHttpKernel** 
