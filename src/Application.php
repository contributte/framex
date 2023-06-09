<?php declare(strict_types = 1);

namespace Contributte\FrameX;

use FrameworkX\App;
use FrameworkX\Container;
use Psr\Container\ContainerInterface;

class Application
{

	private readonly App $app;

	/**
	 * @param array<callable|class-string> $middlewares
	 */
	public function __construct(
		private readonly ContainerInterface $container,
		private readonly array $middlewares
	)
	{
		$this->app = new App(new Container($this->container), ...$this->middlewares);
	}

	/**
	 * @param class-string $handler
	 */
	public function get(string $route, string $handler): void
	{
		$this->app->get($route, $handler);
	}

	/**
	 * @param class-string $handler
	 */
	public function post(string $route, string $handler): void
	{
		$this->app->post($route, $handler);
	}

	/**
	 * @param class-string $handler
	 */
	public function put(string $route, string $handler): void
	{
		$this->app->put($route, $handler);
	}

	/**
	 * @param class-string $handler
	 */
	public function delete(string $route, string $handler): void
	{
		$this->app->delete($route, $handler);
	}

	/**
	 * @param class-string $handler
	 */
	public function options(string $route, string $handler): void
	{
		$this->app->options($route, $handler);
	}

	public function run(): void
	{
		$this->app->run();
	}

}
