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
		'tracy' => [TracyMiddleware::class, ['%debugMode%']],
		'cors' => [CorsMiddleware::class],
	];

	public function getConfigSchema(): Schema
	{
		$expectService = Expect::anyOf(
			Expect::string()->required()->assert(fn ($input) => str_starts_with($input, '@') || class_exists($input) || interface_exists($input)),
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
			'routing' => Expect::array(
				Expect::structure([
					'method' => Expect::string()->required(),
					'path' => Expect::string()->required(),
					'controller' => $expectService,
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

		$builder->addDefinition($this->prefix('application'))
			->setFactory(Application::class)
			->setArguments([
				$this->prefix('@container'),
				array_map(fn (string $service) => $builder->getDefinition($service), array_keys($builder->findByTag(self::MIDDLEWARE_TAG))),
			]);
	}

}
