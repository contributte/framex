<?php declare(strict_types = 1);

namespace Contributte\FrameX\Http;

/**
 * @phpstan-consistent-constructor
 */
abstract class EntityListResponse extends BaseResponse
{

	/** @var mixed[] */
	protected array $entities = [];

	protected ?int $count = null;

	protected ?int $page = null;

	protected ?int $limit = null;

	final public function __construct()
	{
		// Constructor is disabled, use self::create()
	}

	public static function create(): static
	{
		return new static();
	}

	/**
	 * @return mixed[]
	 */
	public function getEntities(): array
	{
		return $this->entities;
	}

	/**
	 * @return mixed[]
	 */
	public function getPayload(): array
	{
		return $this->getEntities();
	}

	/**
	 * @return array<string, scalar>
	 */
	public function getMeta(): array
	{
		$meta = [];

		if ($this->count !== null) {
			$meta['count'] = $this->count;
		}

		if ($this->page !== null) {
			$meta['page'] = $this->page;
		}

		if ($this->limit !== null) {
			$meta['limit'] = $this->limit;
		}

		return $meta;
	}

}
