<?php declare(strict_types = 1);

namespace Contributte\FrameX\Http;

/**
 * @template T
 */
abstract class EntityListResponse extends BaseResponse
{

	/** @var iterable<T> */
	protected iterable $entities = [];

	protected ?int $count = null;

	protected ?int $page = null;

	protected ?int $limit = null;

	final public function __construct()
	{
		// Constructor is disabled, use self::create()
	}

	/**
	 * @return static<T>
	 */
	public static function create(): static
	{
		/** @var static<T> $self */
		$self = new static();

		return $self;
	}

	/**
	 * @return iterable<T>
	 */
	public function getEntities(): iterable
	{
		return $this->entities;
	}

	/**
	 * @return iterable<T>
	 */
	public function getPayload(): iterable
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
