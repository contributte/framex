<?php declare(strict_types = 1);

namespace Contributte\FrameX\Http;

class PureResponse extends BaseResponse
{

	private string $payload;

	public static function create(): self
	{
		return new self();
	}

	public function withPayload(string $payload): self
	{
		$this->payload = $payload;

		return $this;
	}

	public function getPayload(): string
	{
		return $this->payload;
	}

}
