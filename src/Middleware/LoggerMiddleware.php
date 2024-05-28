<?php declare(strict_types = 1);

namespace Contributte\FrameX\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class LoggerMiddleware
{

	public function __construct(
		private LoggerInterface $logger
	)
	{
	}

	public function __invoke(ServerRequestInterface $request, callable $next): mixed
	{
		try {
			return $next($request);
		} catch (Throwable $e) {
			$this->logger->error(sprintf('Error occurred %s', (string) $request->getUri()), ['exception' => $e]);

			throw $e;
		}
	}

}
