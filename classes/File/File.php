<?php

namespace Phoxx\Core\File;

/**
 * https://github.com/symfony/http-foundation/blob/master/File/File.php
 */
class File
{
	private $path;

	public function __construct(string $path, bool $checkPath = true)
	{
        if ($checkPath === true && is_file($path) === false) {
            throw new FileException('Invalid file `'.$path.'`.');
        }
    	$this->path = $path;
	}

	public function getName(): string
	{

	}

	public function getExtension(): string
	{

	}

	public function getMimeType(): string
	{

	}

	public function copy(string $dest): void
	{

	}

	public function move(string $dest): void
	{

	}

	public function delete(): void
	{

	}
}