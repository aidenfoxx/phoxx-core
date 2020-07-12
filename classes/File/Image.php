<?php

namespace Phoxx\Core\File;

use Phoxx\Core\File\FileExceptions\FileException;

class Image extends File
{
  protected $width;

  protected $height;

  public function __construct(string $path)
  {
    parent::__construct();

    $imagick = new Imagick($this->path);

    $this->width = $imagick->getImageWidth();
    $this->height = $imagick->getImageHeight();
  }

  public function getWidth(): int
  {
    return $this->width;
  }

  public function getHeight(): int
  {
    return $this->height;
  }
}
