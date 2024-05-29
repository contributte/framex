<?php declare(strict_types = 1);

namespace Contributte\FrameX\Http;

use Nette\Utils\Validators;
use stdClass;

/** @phpstan-consistent-constructor */
class DataResponse extends BaseResponse
{

	private mixed $data;

	final public function __construct()
	{
		// Constructor is disabled, use self::create()
	}

	public static function create(): static
	{
		return new static();
	}

	public function withDataScalar(string|int|float $data): self
	{
		Validators::assert($data, 'scalar');

		$this->data = $data;

		return $this;
	}

	public function withDataStructure(stdClass $data): self
	{
		$this->data = $data;

		return $this;
	}

	/**
	 * @param array<mixed> $data
	 */
	public function withDataArray(array $data): self
	{
		$this->data = $data;

		return $this;
	}

	public function getData(): mixed
	{
		return $this->data;
	}

	public function getPayload(): mixed
	{
		return $this->getData();
	}

}
