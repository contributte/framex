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
	 * @param array<callable|class-string> $middleware
	 */
	public function get(string $route, string $handler, array $middleware = []): void
	{
		$this->app->get($route, ...[...$middleware, $handler]);
	}

	/**
	 * @param class-string $handler
	 * @param array<callable|class-string> $middleware
	 */
	public function post(string $route, string $handler, array $middleware = []): void
	{
		$this->app->post($route, ...[...$middleware, $handler]);
	}

	/**
	 * @param class-string $handler
	 * @param array<callable|class-string> $middleware
	 */
	public function put(string $route, string $handler, array $middleware = []): void
	{
		$this->app->put($route, ...[...$middleware, $handler]);
	}

	/**
	 * @param class-string $handler
	 * @param array<callable|class-string> $middleware
	 */
	public function delete(string $route, string $handler, array $middleware = []): void
	{
		$this->app->delete($route, ...[...$middleware, $handler]);
	}

	/**
	 * @param class-string $handler
	 * @param array<callable|class-string> $middleware
	 */
	public function options(string $route, string $handler, array $middleware = []): void
	{
		$this->app->options($route, ...[...$middleware, $handler]);
	}

	public function run(): void
	{
		$this->app->run();
	}

}
