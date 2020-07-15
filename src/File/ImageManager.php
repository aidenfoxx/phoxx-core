<?php

namespace Phoxx\Core\File;

use Phoxx\Core\File\FileExceptions\FileException;

class ImageManager
{
  public function compress(float $quality): void
  {
    $this->imagick->setImageCompressionQuality($quality);

    if ($this->imagick->writeImage($this->path) === false) {
      throw new FileException('Failed to write image `' . $this->path . '`.');
    }
  }

  public function convert(string $dest, string $format = self::FORMAT_JPG, string $background = '#ffffff'): void
  {
    $this->imagick->setImageBackgroundColor($background);

    switch ($format) {
      case Image::FORMAT_BMP:
        $this->imagick->setImageFormat('bmp');
        break;

      case Image::FORMAT_PNG:
        $this->imagick->setImageFormat('png');
        break;

      default:
        $this->imagick->setImageFormat('jpg');
        break;
    }

    if ($this->imagick->writeImage($dest) === false) {
      throw new FileException('Failed to write image `' . $dest . '`.');
    }

    $this->path = $dest;
  }

  public function resize(int $width, int $height = 0, string $scale = self::SCALE_FILL, string $background = '#ffffff'): void
  {
    if ($width > 0 && $height > 0) {
      $sourceWidth = $this->imagick->getImageWidth();
      $sourceHeight = $this->imagick->getImageHeight();
      $sourceRatio = $sourceWidth / $sourceHeight;

      $resizeRatio = $width / $height;

      switch ($scale) {
        case self::SCALE_COVER:
          if ($sourceRatio > $resizeRatio) {
            $coverWidth = $sourceHeight * $resizeRatio;
            $offset = $coverWidth / 2 - $sourceWidth / 2;

            $this->imagick->extentImage($coverWidth, $sourceHeight, -$offset, 0);
          } elseif ($sourceRatio < $resizeRatio) {
            $coverHeight = $sourceWidth / $resizeRatio;
            $offset = $coverHeight / 2 - $sourceHeight / 2;

            // imagecopyresampled
            $this->imagick->extentImage($sourceWidth, $coverHeight, 0, -$offset);
          }
          break;

        case self::SCALE_CONTAIN:
          $this->imagick->setImageBackgroundColor($background);

          if ($sourceRatio > $resizeRatio) {
            $containHeight = $sourceWidth / $resizeRatio;
            $offset = $containHeight / 2 - $sourceHeight / 2;

            $this->imagick->extentImage($sourceWidth, $containHeight, 0, -$offset);
          } elseif ($sourceRatio < $resizeRatio) {
            $containWidth = $sourceHeight * $resizeRatio;
            $offset = $containWidth / 2 - $sourceWidth / 2;

            $this->imagick->extentImage($containWidth, $sourceHeight, -$offset, 0);
          }
          break;
      }
    }

    $this->imagick->scaleImage($width, $height);

    if ($this->imagick->writeImage($this->path) === false) {
      throw new FileException('Failed to write image `' . $this->path . '`.');
    }
  }

  public function rotate(int $angle, string $background = '#ffffff'): void
  {
    $this->imagick->rotateImage($angle, $background);

    if ($this->imagick->writeImage($this->path) === false) {
      throw new FileException('Failed to write image `' . $this->path . '`.');
    }
  }
}
