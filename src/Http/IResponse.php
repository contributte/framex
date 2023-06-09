<?php declare(strict_types = 1);

namespace Contributte\FrameX\Http;

interface IResponse
{

	/** @return array<string, string> */
	public function getHeaders(): array;

	public function getStatusCode(): int;

	public function getPayload(): mixed;

}
