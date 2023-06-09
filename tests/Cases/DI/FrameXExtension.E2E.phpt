<?php declare(strict_types = 1);

use Contributte\FrameX\Application;
use Contributte\FrameX\DI\FrameXExtension;
use Contributte\Tester\Toolkit;
use Contributte\Tester\Utils\ContainerBuilder;
use Contributte\Tester\Utils\Liberator;
use Contributte\Tester\Utils\Neonkit;
use Nette\DI\Compiler;
use React\Http\Message\ServerRequest;
use Tester\Assert;
use Tests\Fixtures\TestHandler;

require_once __DIR__ . '/../../bootstrap.php';

// E2E
Toolkit::test(function (): void {
	$container = ContainerBuilder::of()
		->withCompiler(function (Compiler $compiler): void {
			$compiler->addExtension('framex', new FrameXExtension());
			$compiler->addConfig(Neonkit::load(<<<'NEON'
				framex:
					middlewares: []
					routing:
						- { path: /test, method: get, controller: Tests\Fixtures\TestController }

				services:
					- Tests\Fixtures\TestController
			NEON
			));
		})
		->build();

	/** @var Application $app */
	$app = $container->getByType(Application::class);

	// Test to /test endpoint
	$sapi = TestHandler::of(new ServerRequest('GET', '/test'));
	Liberator::of(Liberator::of($app)->app)->sapi = $sapi;
	$app->run();

	Assert::equal(200, $sapi->response->getStatusCode());
	Assert::equal('test', $sapi->response->getBody()->getContents());

	// Send to fake address
	$sapi = TestHandler::of(new ServerRequest('GET', '/fake'));
	Liberator::of(Liberator::of($app)->app)->sapi = $sapi;
	$app->run();

	Assert::equal(404, $sapi->response->getStatusCode());
	Assert::match('%A%Error 404: Page Not Found%A%', $sapi->response->getBody()->getContents());

	// Send to invalid HTTP method
	$sapi = TestHandler::of(new ServerRequest('POST', '/test'));
	Liberator::of(Liberator::of($app)->app)->sapi = $sapi;
	$app->run();

	Assert::equal(405, $sapi->response->getStatusCode());
	Assert::match('%A%Error 405: Method Not Allowed%A%', $sapi->response->getBody()->getContents());
});
