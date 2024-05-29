<?php declare(strict_types = 1);

namespace Contributte\FrameX\Middleware;

use Contributte\FrameX\Debug\TracyDebugger;
use Contributte\FrameX\Exception\LogicalException;
use Contributte\FrameX\Http\EntityListResponse;
use Contributte\FrameX\Http\ErrorResponse;
use Contributte\FrameX\Http\IResponse;
use Contributte\FrameX\Http\PureResponse;
use Nette\Utils\Json;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Io\BufferedBody;
use React\Http\Message\Response;
use Throwable;

class NegotiationV2Middleware
{

	public function __construct(
		private bool $catchExceptions = true,
	)
	{
	}

	private function handleResponse(ServerRequestInterface $request, IResponse $apiResponse): ResponseInterface
	{
		$response = new Response();
		$response = $response->withStatus($apiResponse->getStatusCode());
		$response = $response->withHeader('content-type', 'application/json');

		foreach ($apiResponse->getHeaders() as $key => $value) {
			$response = $response->withHeader($key, $value);
		}

		if ($apiResponse instanceof PureResponse) {
			return $response->withBody(
				new BufferedBody($apiResponse->getPayload())
			);
		}

		if ($apiResponse instanceof EntityListResponse) {
			$output = [
				'status' => 'ok',
				'data' => $apiResponse->getEntities(),
			];

			$meta = $apiResponse->getMeta();

			if ($meta !== []) {
				$output['meta'] = $meta;
			}

			return $response->withBody(
				new BufferedBody(
					Json::encode($output)
				)
			);
		}

		if ($apiResponse instanceof ErrorResponse) {
			return $response->withBody(
				new BufferedBody(
					Json::encode([
						'status' => 'error',
						'data' => $apiResponse->getPayload(),
					])
				)
			);
		}

		return $response->withBody(
			new BufferedBody(
				Json::encode([
					'status' => 'ok',
					'data' => $apiResponse->getPayload(),
				])
			)
		);
	}

	private function handleError(ServerRequestInterface $request, Throwable $e): ResponseInterface
	{
		$response = new Response();
		$response = $response->withStatus(Response::STATUS_INTERNAL_SERVER_ERROR);
		$response = $response->withHeader('content-type', 'application/json');

		$response = $response->withBody(
			new BufferedBody(
				Json::encode([
					'status' => 'error',
					'error' => [
						'code' => Response::STATUS_INTERNAL_SERVER_ERROR,
						'message' => $e->getMessage() !== '' ? $e->getMessage() : 'Internal server error',
					],
				])
			)
		);

		return $response;
	}

	public function __invoke(ServerRequestInterface $request, callable $next): ResponseInterface
	{
		try {
			$apiResponse = $next($request);

			// If middleware returns PSR-7 response, do not negotiate
			if ($apiResponse instanceof ResponseInterface) {
				return $apiResponse;
			}

			if (!($apiResponse instanceof IResponse)) {
				throw new LogicalException(sprintf('Response from controller/middleware must be instanceof "%s"', IResponse::class));
			}

			return $this->handleResponse($request, $apiResponse);
		} catch (Throwable $e) {
			if (!$this->catchExceptions) {
				TracyDebugger::catch($e);
			}

			return $this->handleError($request, $e);
		}
	}

}
