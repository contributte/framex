<?php declare(strict_types = 1);

namespace Tests\Fixtures;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class TestController
{

	public function __invoke(ServerRequestInterface $request): ResponseInterface
	{
		return Response::html('test');
	}

}
