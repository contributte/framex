<?php declare(strict_types = 1);

namespace Contributte\FrameX\Middleware;

use Contributte\FrameX\Exception\LogicalException;
use Contributte\FrameX\Http\IResponse;
use Contributte\FrameX\Http\PureResponse;
use Nette\Utils\Json;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use Throwable;
use function RingCentral\Psr7\stream_for;

class NegotiationMiddleware
{

	private function handleResponse(ServerRequestInterface $request, IResponse $apiResponse): ResponseInterface
	{
		$response = new Response();
		$response = $response->withStatus($apiResponse->getStatusCode());

		foreach ($apiResponse->getHeaders() as $key => $value) {
			$response = $response->withHeader($key, $value);
		}

		// Only pure response will be not converted to JSON.
		$payload = $apiResponse instanceof PureResponse ? $apiResponse->getPayload() : Json::encode($apiResponse->getPayload());

		return $response->withBody(stream_for($payload));
	}

	private function handleError(ServerRequestInterface $request, Throwable $e): ResponseInterface
	{
		$response = new Response();
		$response = $response->withStatus(Response::STATUS_INTERNAL_SERVER_ERROR);

		$response = $response->withBody(stream_for(
			Json::encode(['code' => Response::STATUS_INTERNAL_SERVER_ERROR, 'message' => $e->getMessage() !== '' ? $e->getMessage() : '{"error": "Internal server error"}'])
		));

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
			return $this->handleError($request, $e);
		}
	}

}
