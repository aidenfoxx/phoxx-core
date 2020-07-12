<?php

namespace Phoxx\Core\Http;

use Phoxx\Core\Http\Exceptions\ResponseException;

class Response
{
	protected $content;

	protected $status;

	protected $headers = [];

	public function __construct(string $content = '', int $status = Response::HTTP_OK, array $headers = [])
	{
		$this->content = $content;
		$this->status = $status;
		$this->headers = $headers;
	}

	public function getContent(): string
	{
		return $this->content;
	}

	public function getStatus(): int
	{
		return $this->status;
	}

	public function getHeader(string $key): ?string
	{
		return isset($this->headers[$key]) === true ? (string)$this->headers[$key] : null;
	}

	public function setHeader(string $key, string $value): void
	{
		$this->headers[$key] = $value;
	}

	public function removeHeader(string $key): void
	{
		unset($this->headers[$key]);
	}

	public function getHeaders(): array
	{
		return $this->headers;
	}
}
