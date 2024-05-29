# Contributte FrameX

## Content

- [Setup](#setup)
- [Configuration](#configuration)
- [Controller](#controller)
- [Entrypoint & Index](#entrypoint--index)
- [Examples](#examples)

## Setup

Install composer package.

```bash
composer require contributte/framex
```

Register Nette extension.

```neon
extensions:
	framex: Contributte\FrameX\DI\FrameXExtension
```

## Configuration

Minimal configuration could look like this:

```neon
framex:
	routing:
		- path: /v1/ping
		  method: GET
		  controller: App\PingController

services:
	- App\PingController
```

Full configuration could look like this:

```neon
framex:
	# List of middlewares (position mathers)
	middlewares:
		tracy: Contributte\FrameX\Middleware\TracyMiddleware(%debugMode%)
		cors: Contributte\FrameX\Middleware\CorsMiddleware
		negotiation: Contributte\FrameX\Middleware\NegotiationMiddleware
		# negotiationv2: Contributte\FrameX\Middleware\NegotiationV2Middleware

	# List of routes
	routing:
		- path: /v1/ping
		  method: GET
		  controller: App\PingController

		- path: /v1/job
		  method: POST
		  controller: App\CreateJobController

services:
    - App\PingController
    - App\CreateJobController
```

See more about Framework X at [official documentation](https://framework-x.org/).

## Controller

Controller is class that handles incoming HTTP request and returns HTTP response. Request & response are part of PSR-7.
Controller must be registered as service to [Nette DIC](https://doc.nette.org/en/dependency-injection/nette-container).

```php
<?php declare(strict_types = 1);

namespace App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

final class PingController
{

	public function __invoke(ServerRequestInterface $request): ResponseInterface
	{
		return Response::html('pong');
	}

}
```

## Entrypoint & Index

In your `index.php` or somewhere else get `Contributte\FrameX\Application::class` from [Nette DIC](https://doc.nette.org/en/dependency-injection/nette-container) and call **run** method.

```
<?php

require __DIR__ . '/../vendor/autoload.php';

$container = Bootstrap::boot();
$app = $container->getByType(Contributte\FrameX\Application::class);
$app->run();
```

## Examples

There is example project [contributte/framex-skeleton](https://github.com/contributte/framex-skeleton).
