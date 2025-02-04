<?php declare(strict_types = 1);

namespace Contributte\FrameX\Http;

abstract class BaseResponse implements IResponse
{

	/** @var array<string, string> */
	protected array $headers = [];

	protected int $statusCode = 200;

	public function getStatusCode(): int
	{
		return $this->statusCode;
	}

	/**
	 * @return array<string, string>
	 */
	public function getHeaders(): array
	{
		return $this->headers;
	}

	public function withStatusCode(int $code): static
	{
		$this->statusCode = $code;

		return $this;
	}

	/**
	 * @param array<string, string> $headers
	 */
	public function withHeaders(array $headers): static
	{
		$this->headers = $headers;

		return $this;
	}

}
