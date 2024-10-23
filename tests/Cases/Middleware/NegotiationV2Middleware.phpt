<?php declare(strict_types = 1);

use Contributte\FrameX\Http\PureResponse;
use Contributte\FrameX\Middleware\NegotiationV2Middleware;
use Contributte\Tester\Toolkit;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Http\Message\ServerRequest;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// String payload
Toolkit::test(function (): void {
	$middleware = new NegotiationV2Middleware();

	$request = new ServerRequest('GET', 'http://localhost:8080/');
	$response = PureResponse::create()->withPayload('test');
	$next = fn (ServerRequestInterface $request) => $response;

	$return = $middleware($request, $next);

	Assert::type(Response::class, $return);
	Assert::equal(['content-type' => ['application/json']], $return->getHeaders());
	Assert::equal('test', $return->getBody()->getContents());
});

// Empty
Toolkit::test(function (): void {
	$middleware = new NegotiationV2Middleware();

	$request = new ServerRequest('GET', 'http://localhost:8080/');
	$response = PureResponse::create();
	$next = fn (ServerRequestInterface $request) => $response;

	$return = $middleware($request, $next);

	Assert::type(Response::class, $return);
	Assert::equal(['content-type' => ['application/json']], $return->getHeaders());
	Assert::equal('', $return->getBody()->getContents());
});
