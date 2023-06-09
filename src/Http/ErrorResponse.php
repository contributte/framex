<?php declare(strict_types = 1);

namespace Contributte\FrameX\Http;

class ErrorResponse extends BaseResponse
{

	private ?int $errorCode = null;

	private ?string $message = null;

	/** @var mixed[] */
	private array $validations = [];

	public static function create(): self
	{
		return new self();
	}

	public function getErrorCode(): ?int
	{
		return $this->errorCode;
	}

	public function withErrorCode(int $errorCode): self
	{
		$this->errorCode = $errorCode;

		return $this;
	}

	public function getMessage(): ?string
	{
		return $this->message;
	}

	public function withMessage(?string $message): self
	{
		$this->message = $message ?? 'Something went wrong';

		return $this;
	}

	/**
	 * @return mixed[]
	 */
	public function getValidations(): array
	{
		return $this->validations;
	}

	/**
	 * @param mixed[] $validations
	 */
	public function withValidations(array $validations): self
	{
		$this->validations = $validations;

		return $this;
	}

	/**
	 * @return array<string, int|string|array<mixed>>
	 */
	public function getPayload(): array
	{
		$output = [];

		if ($this->errorCode !== null) {
			$output['code'] = $this->errorCode;
		}

		if ($this->message !== null) {
			$output['message'] = $this->message;
		}

		if ($this->validations !== []) {
			$output['validations'] = $this->validations;
		}

		return $output;
	}

}
