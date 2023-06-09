<?php declare(strict_types = 1);

namespace Contributte\FrameX\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class CorsMiddleware
{

	public function __invoke(ServerRequestInterface $request, callable $next): ResponseInterface
	{
		if (strtoupper($request->getMethod()) === 'OPTIONS') {
			return new Response(200, [
				'Access-Control-Allow-Origin' => '*',
				'Access-Control-Allow-Methods' => '*',
				'Access-Control-Allow-Headers' => '*',
				'Access-Control-Allow-Credentials' => 'true',
			]);
		}

		/** @var ResponseInterface $response */
		$response = $next($request);

		return $response->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Methods', '*')
			->withHeader('Access-Control-Allow-Headers', '*')
			->withHeader('Access-Control-Allow-Credentials', 'true');
	}

}
