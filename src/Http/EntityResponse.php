<?php declare(strict_types = 1);

namespace Contributte\FrameX\Http;

/** @phpstan-consistent-constructor */
abstract class EntityResponse extends BaseResponse
{

	/** @var mixed[] */
	protected array $payload = [];

	final public function __construct()
	{
		// Constructor is disabled, use self::create()
	}

	public static function create(): self
	{
		return new static();
	}

	/**
	 * @return mixed[]
	 */
	public function getPayload(): array
	{
		return $this->payload;
	}

}
