<?php declare(strict_types = 1);

use Contributte\FrameX\Application;
use Contributte\FrameX\DI\FrameXExtension;
use Contributte\Tester\Toolkit;
use Contributte\Tester\Utils\ContainerBuilder;
use Contributte\Tester\Utils\Liberator;
use Contributte\Tester\Utils\Neonkit;
use Nette\DI\Compiler;
use Nette\DI\InvalidConfigurationException;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Basic
Toolkit::test(function (): void {
	$container = ContainerBuilder::of()
		->withCompiler(function (Compiler $compiler): void {
			$compiler->addExtension('framex', new FrameXExtension());
			$compiler->addConfig([
				'parameters' => [
					'debugMode' => false,
				],
			]);
			$compiler->addConfig(Neonkit::load(<<<'NEON'
				framex:
					routing:
						- { path: test, method: get, controller: test }
			NEON
			));
		})
		->build();

	Assert::type(Application::class, $container->getByType(Application::class));
	Assert::count(2, $container->findByTag(FrameXExtension::MIDDLEWARE_TAG));
});

// No default middlewares
Toolkit::test(function (): void {
	$container = ContainerBuilder::of()
		->withCompiler(function (Compiler $compiler): void {
			$compiler->addExtension('framex', new FrameXExtension());
			$compiler->addConfig(Neonkit::load(<<<'NEON'
				framex:
					middlewares: []
					routing: []
			NEON
			));
		})
		->build();

	Assert::type(Application::class, $container->getByType(Application::class));
	Assert::count(0, $container->findByTag(FrameXExtension::MIDDLEWARE_TAG));
});

// Middlewares
Toolkit::test(function (): void {
	$container = ContainerBuilder::of()
		->withCompiler(function (Compiler $compiler): void {
			$compiler->addExtension('framex', new FrameXExtension());
			$compiler->addConfig([
				'parameters' => [
					'debugMode' => false,
				],
			]);
			$compiler->addConfig(Neonkit::load(<<<'NEON'
				framex:
					routing:
						- { path: test, method: get, controller: test }
			NEON
			));
		})
		->build();

	$tracyMiddleware = $container->getService('framex.middleware.tracy');
	Assert::false(Liberator::of($tracyMiddleware)->logExceptions);
	Assert::false(Liberator::of($tracyMiddleware)->handleExceptions);
});

// No routing
Toolkit::test(function (): void {
	Assert::exception(
		function (): void {
			ContainerBuilder::of()
				->withCompiler(function (Compiler $compiler): void {
					$compiler->addExtension('framex', new FrameXExtension());
				})
				->build();
		},
		InvalidConfigurationException::class,
		"The mandatory item 'framex › routing' is missing."
	);
});
