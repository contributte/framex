<?php declare(strict_types = 1);

namespace Contributte\FrameX\Http;

/** @phpstan-consistent-constructor */
class PureResponse extends BaseResponse
{

	private ?string $payload = null;

	final public function __construct()
	{
		// Constructor is disabled, use self::create()
	}

	public static function create(): static
	{
		return new static();
	}

	public function withPayload(?string $payload): self
	{
		$this->payload = $payload;

		return $this;
	}

	public function getPayload(): ?string
	{
		return $this->payload;
	}

}
