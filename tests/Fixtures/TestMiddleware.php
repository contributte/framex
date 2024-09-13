<?php declare(strict_types = 1);

namespace Tests\Fixtures;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class TestMiddleware
{

	public function __invoke(ServerRequestInterface $request, callable $next): ResponseInterface
	{
		return Response::plaintext('secured')->withStatus(401);
	}

}
