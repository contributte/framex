<?php declare(strict_types = 1);

namespace Contributte\FrameX\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use Tracy\Debugger;

class TracyMiddleware
{

	public function __construct(private readonly bool $enable)
	{
	}

	public function __invoke(ServerRequestInterface $request, callable $next): mixed
	{
		try {
			return $next($request);
		} catch (Throwable $e) {
			echo $e->getMessage();
			if ($this->enable) {
				Debugger::getStrategy()->handleException($e, true);
			}

			throw $e;
		}
	}

}
