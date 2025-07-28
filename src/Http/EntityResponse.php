<?php declare(strict_types = 1);

namespace Contributte\FrameX\Http;

/**
 * @template T
 */
abstract class EntityResponse extends BaseResponse
{

	/** @var T */
	protected mixed $payload;

	/**
	 * @return T
	 */
	public function getPayload(): mixed
	{
		return $this->payload;
	}

}
