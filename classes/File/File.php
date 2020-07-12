<?php

namespace Phoxx\Core\File;

use Phoxx\Core\File\FileExceptions\FileException;

class File
{
  protected $path;

  protected $name;

  protected $directoryName;

  protected $baseName;

  protected $extension;

  protected $mimetype;

  public function __construct(string $path)
  {
    if (is_file($path) === false) {
      throw new FileException('Invalid file `'.$path.'`.');
    }

    $pathInfo = pathinfo($this->path);

    $this->path = realpath($path);
    $this->name = $pathInfo['filename'];
    $this->directoryName = $pathInfo['dirname'];
    $this->baseName = $pathInfo['basename'];
    $this->extension = $pathInfo['extension'];
    $this->mimetype ($mimetype = @mime_content_type($path)) !== false ? $mimetype : 'text/plain';
  }

  public function getPath(): string
  {
    return $this->path;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function getDirectoryName(): string
  {
    return $this->directoryName;
  }

  public function getBaseName(): string
  {
    return $this->baseName;
  }

  public function getExtension(): string
  {
    return $this->extension;
  }

  public function getMimetype(): string
  {
    return $this->mimetype;
  }
}
