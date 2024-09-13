<?php declare(strict_types = 1);

namespace Contributte\FrameX\DI;

use Contributte\FrameX\Application;
use Contributte\FrameX\Bridge\NetteInteropContainer;
use Contributte\FrameX\Middleware\CorsMiddleware;
use Contributte\FrameX\Middleware\TracyMiddleware;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use Nette\DI\Helpers;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;

/**
 * @method stdClass getConfig()
 */
class FrameXExtension extends CompilerExtension
{

	public const MIDDLEWARE_TAG = 'contributte.framex.middleware';

	public const DEFAULT_MIDDLEWARES = [
		'tracy' => [TracyMiddleware::class, ['handleExceptions' => '%debugMode%']],
		'cors' => [CorsMiddleware::class],
	];

	public function getConfigSchema(): Schema
	{
		$expectService = Expect::anyOf(
			Expect::string()->required()->assert(fn ($input) => str_starts_with($input, '@') || class_exists($input) || interface_exists($input), 'not a valid service definition'),
			Expect::type(Statement::class),
		)->required();

		return Expect::structure([
			'middlewares' => Expect::anyOf(
				Expect::null(),
				Expect::arrayOf(
					$expectService,
					Expect::string()->required(),
				)
			),
			'routing' => Expect::arrayOf(
				Expect::structure([
					'method' => Expect::anyOf('get', 'post', 'put', 'delete', 'options')->before(fn ($method) => strtolower($method))->required(),
					'path' => Expect::string()->required(),
					'controller' => $expectService,
					'middleware' => Expect::arrayOf($expectService),
				])->required()
			)->required(),
		]);
	}

	public function loadConfiguration(): void
	{
		$config = $this->getConfig();
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('container'))
			->setFactory(NetteInteropContainer::class)
			->setAutowired(false);

		// Use default middlewares or user defined
		if ($config->middlewares === null) {
			foreach (self::DEFAULT_MIDDLEWARES as $name => $middleware) {
				$builder->addDefinition($this->prefix(sprintf('middleware.%s', $name)))
					->setFactory($middleware[0])
					->setArguments(array_map(fn ($item) => Helpers::expand($item, $builder->parameters), $middleware[1] ?? []))
					->setAutowired(false)
					->addTag(self::MIDDLEWARE_TAG, $name);
			}
		} else {
			foreach ($config->middlewares as $name => $factory) {
				$builder->addDefinition($this->prefix(sprintf('middleware.%s', $name)))
					->setFactory($factory)
					->setAutowired(false)
					->addTag(self::MIDDLEWARE_TAG, $name);
			}
		}

		$applicationDef = $builder->addDefinition($this->prefix('application'));
		$applicationDef->setFactory(Application::class)
			->setArguments([
				$this->prefix('@container'),
				array_map(fn (string $service) => $builder->getDefinition($service), array_keys($builder->findByTag(self::MIDDLEWARE_TAG))),
			]);

		foreach ($config->routing as $route) {
			$applicationDef->addSetup(strtolower($route->method), [$route->path, $route->controller, $route->middleware]);
		}
	}

}
