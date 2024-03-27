<?php declare(strict_types = 1);

namespace Contributte\FrameX\Debug;

use Contributte\FrameX\Exception\LogicalException;
use Throwable;
use Tracy\Debugger;

final class TracyDebugger
{

	public static function catch(Throwable $e): void
	{
		if (!class_exists(Debugger::class)) {
			throw new LogicalException('Missing tracy/tracy package.');
		}

		Debugger::getStrategy()->handleException($e, true);
	}

}
