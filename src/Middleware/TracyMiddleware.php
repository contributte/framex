<?php declare(strict_types = 1);

namespace Contributte\FrameX\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use Tracy\Debugger;
use Tracy\ILogger;

class TracyMiddleware
{

	public function __construct(
		private readonly bool $handleExceptions = false,
		private readonly bool $logExceptions = false,
		private readonly string $logLevel = ILogger::EXCEPTION,
	)
	{
	}

	public function __invoke(ServerRequestInterface $request, callable $next): mixed
	{
		try {
			return $next($request);
		} catch (Throwable $e) {
			if ($this->logExceptions) {
				Debugger::log($e, $this->logLevel);
			}

			if ($this->handleExceptions) {
				Debugger::getStrategy()->handleException($e, true);
			}

			throw $e;
		}
	}

}
