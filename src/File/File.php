<?php

namespace Phoxx\Core\File;

use Phoxx\Core\File\Exceptions\FileException;

class File
{
    protected $path;

    protected $name;

    protected $baseName;

    protected $directory;

    protected $extension;

    protected $mimetype;

    public function __construct(string $path)
    {
        if (!is_file($path)) {
            throw new FileException('Invalid file `' . $path . '`.');
        }

        $this->path = realpath($path);
        $this->mimetype = ($mimetype = @mime_content_type($path)) ? $mimetype : 'text/plain';

        $pathInfo = pathinfo($path);

        $this->name = $pathInfo['filename'];
        $this->baseName = $pathInfo['basename'];
        $this->directory = $pathInfo['dirname'] ?? null;
        $this->extension = $pathInfo['extension'] ?? null;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBaseName(): string
    {
        return $this->baseName;
    }

    public function getDirectory(): ?string
    {
        return $this->directory;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function getMimetype(): string
    {
        return $this->mimetype;
    }
}
