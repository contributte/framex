<?php declare(strict_types = 1);

namespace Contributte\FrameX\Middleware;

use Contributte\FrameX\Debug\TracyDebugger;
use Contributte\FrameX\Exception\LogicalException;
use Contributte\FrameX\Http\DataResponse;
use Contributte\FrameX\Http\ErrorResponse;
use Contributte\FrameX\Http\IResponse;
use Nette\Utils\Json;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Io\BufferedBody;
use React\Http\Message\Response;
use Throwable;

class NegotiationMiddleware
{

	public function __construct(
		private bool $catchExceptions = true
	)
	{
	}

	private function handleResponse(ServerRequestInterface $request, IResponse $apiResponse): ResponseInterface
	{
		$response = new Response();
		$response = $response->withStatus($apiResponse->getStatusCode());
		$response = $response->withHeader('Content-Type', 'application/json');

		foreach ($apiResponse->getHeaders() as $key => $value) {
			$response = $response->withHeader($key, $value);
		}

		if ($apiResponse instanceof DataResponse) {
			$response = $response->withBody(
				new BufferedBody(
					Json::encode($apiResponse->getPayload())
				)
			);
		} elseif ($apiResponse instanceof ErrorResponse) {
			$response = $response->withBody(
				new BufferedBody(
					(string) $apiResponse->getMessage()
				)
			);
		} else {
			/** @var string $payload */
			$payload = $apiResponse->getPayload();
			$response = $response->withBody(
				new BufferedBody(
					$payload
				)
			);
		}

		return $response;
	}

	private function handleError(ServerRequestInterface $request, Throwable $e): ResponseInterface
	{
		$response = new Response();
		$response = $response->withStatus(Response::STATUS_INTERNAL_SERVER_ERROR);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response = $response->withBody(
			new BufferedBody(
				Json::encode([
					'code' => Response::STATUS_INTERNAL_SERVER_ERROR,
					'message' => $e->getMessage(),
					'trace' => $e->getTrace(),
				])
			)
		);

		return $response;
	}

	public function __invoke(ServerRequestInterface $request, callable $next): ResponseInterface
	{
		try {
			$apiResponse = $next($request);

			// Bypass negotiation if response is already PSR-7
			if ($apiResponse instanceof ResponseInterface) {
				return $apiResponse;
			}

			// Double check if response is our IResponse
			if (!($apiResponse instanceof IResponse)) {
				throw new LogicalException(sprintf('Response from controller must be instanceof "%s", given "%s"', IResponse::class, $apiResponse::class));
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
