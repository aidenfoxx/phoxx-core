<?php

namespace Phoxx\Core\File;

use Phoxx\Core\File\Exceptions\ImageException;

class Image extends File
{
  public const FORMAT_JPG = 'jpg';

  public const FORMAT_BMP = 'bmp';

  public const FORMAT_PNG = 'png';

  public const FORMAT_GIF = 'gif';

  public const SCALE_FILL = 'fill';

  public const SCALE_COVER = 'cover';

  public const SCALE_CONTAIN = 'contain';

  protected $format;

  public function __construct(string $path)
  {
    parent::__construct($path);

    switch (@exif_imagetype($path)) {
      case IMAGETYPE_BMP:
        $this->format = self::FORMAT_BMP;
        break;

      case IMAGETYPE_PNG:
        $this->format = self::FORMAT_PNG;
        break;

      case IMAGETYPE_GIF:
        $this->format = self::FORMAT_GIF;
        break;

      case IMAGETYPE_JPEG:
        $this->format = self::FORMAT_JPG;
        break;

      default:
        throw new ImageException('Unsupported image format.');
        break;
    }
  }

  public function getFormat(): string
  {
    return $this->format;
  }

  public function getWidth(): int
  {
    return (int)@getimagesize($this->getPath())[0];
  }

  public function getHeight(): int
  {
    return (int)@getimagesize($this->getPath())[1];
  }
}
