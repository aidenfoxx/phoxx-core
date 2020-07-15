<?php

namespace Phoxx\Core\File;

use Phoxx\Core\File\FileExceptions\FileException;

class Image extends File
{
  public const FORMAT_JPG = 'jpg';

  public const FORMAT_BMP = 'bmp';

  public const FORMAT_PNG = 'png';

  public const SCALE_FILL = 'fill';

  public const SCALE_COVER = 'cover';

  public const SCALE_CONTAIN = 'contain';

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
