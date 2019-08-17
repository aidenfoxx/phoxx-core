<?php

namespace Phoxx\Core\File;

use Phoxx\Core\File\FileExceptions\FileFileException;

class File
{
	protected $path;

	public function __construct(string $path)
	{
		if (is_file($path) === false) {
			throw new FileException('Invalid file `'.$path.'`.');
		}

		$this->path = $path;
	}

	public function getPath(): string
	{
		return $this->path;
	}

	public function getName(): string
	{
		return pathinfo($this->path, PATHINFO_FILENAME);
	}

	public function getBaseName(): string
	{
		return pathinfo($this->path, PATHINFO_BASENAME);
	}

	public function getExtension(): string
	{
		return pathinfo($this->path, PATHINFO_EXTENSION);
	}

	public function getMimeType(): string
	{
		return ($mimetype = @mime_content_type($this->path)) !== false ? $mimetype : 'text/plain';
	}

	public function copy(string $dest): void
	{
		if (@copy($this->path, $dest) === false) {
			throw new FileException('Failed to copy file to destination `'.$dest.'`.');
		}

		$this->path = $dest;
	}

	public function move(string $dest): void
	{
		if (@rename($this->path, $dest) === false) {
			throw new FileException('Failed to move file to destination `'.$dest.'`.');
		}

		$this->path = $dest;
	}

	public function delete(): void
	{
		if (@unlink($this->path) === false) {
			throw new FileException('Failed to remove file `'.$this->path.'`.');
		}
	}
}