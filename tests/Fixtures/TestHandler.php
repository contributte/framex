<?php declare(strict_types = 1);

namespace Tests\Fixtures;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\ServerRequest;

class TestHandler
{

	public ServerRequestInterface $request;

	public ResponseInterface $response;

	public static function of(ServerRequest $param): self
	{
		$self = new self();
		$self->request = $param;

		return $self;
	}

	public function run(callable $handler): void
	{
		$this->response = $handler($this->request);
	}

}
