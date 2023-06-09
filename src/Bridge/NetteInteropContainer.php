<?php declare(strict_types = 1);

namespace Contributte\FrameX\Bridge;

use Nette\DI\Container;
use Psr\Container\ContainerInterface;

final class NetteInteropContainer implements ContainerInterface
{

	public function __construct(private readonly Container $container)
	{
	}

	/**
	 * @param class-string $service
	 */
	public function get(string $service): object
	{
		return $this->container->getByType($service);
	}

	/**
	 * @param class-string $service
	 */
	public function has(string $service): bool
	{
		return $this->container->getByType($service, false) !== null;
	}

}
