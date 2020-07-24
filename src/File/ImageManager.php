<?php

namespace Phoxx\Core\File;

class ImageManager
{
  private function parseImage(Image $image)
  {
    switch ($image->getFormat()) {
      case Image::FORMAT_BMP:
        $resource = imagecreatefrombmp($image->getPath());
        break;

      case Image::FORMAT_PNG:
        $resource = imagecreatefrompng($image->getPath());
        break;

      case Image::FORMAT_GIF:
        $resource = imagecreatefromgif($image->getPath());
        break;

      default:
        $resource = imagecreatefromjpeg($image->getPath());
        break;
    }

    if ($resource === false) {
      throw new ImageException('Failed to parse image `' . $image->getPath() . '`.');
    }

    return $resource;
  }

  private function writeImage($resource, string $dest, string $format, int $quality = -1): void
  {
    switch ($format) {
      case Image::FORMAT_BMP:
        $response = imagebmp($resource, $dest, $quality);
        break;

      case Image::FORMAT_PNG:
        $response = imagepng($resource, $dest, $quality);
        break;

      case Image::FORMAT_GIF:
        $response = imagegif($resource, $dest, $quality);
        break;

      default:
        $response = imagejpeg($resource, $dest, $quality);
        break;
    }

    if ($response === false) {
      throw new ImageException('Failed to write image `' . $image->getPath() . '`.');
    }
  }

  public function compress(Image $image, float $quality = -1): void
  {
    $source = $this->parseImage($image);

    $this->writeImage($source, $image->getPath(), $image->getFormat(), $quality);

    imagedestroy($source);
  }

  public function resize(Image $image, int $width, int $height = -1, string $scale = Image::SCALE_FILL, ?array $background = null, float $quality = -1): void
  {
    if ($width === 0 || $height === 0 || $width < 0 && $height < 0) {
      throw new ImageException('Invalid resize dimensions.');
    }

    $offsetX = 0;
    $offsetY = 0;

    $sourceRatio = $image->getWidth() / $image->getHeight();

    /**
     * Generate height/width to match source
     * aspect ratio.
     */
    $width = $width < 0 ? $height * $sourceRatio : $width;
    $height = $height < 0 ? $width * $sourceRatio : $height;

    $output = imagecreatetruecolor($width, $height);

    if ($scale === Image::SCALE_COVER || $scale === Image::SCALE_CONTAIN) {
      $resizeWidth = $width;
      $resizeHeight = $height;
      $resizeRatio = $width / $height;

      if (
        $scale === Image::SCALE_COVER && $resizeRatio > $sourceRatio ||
        $scale === Image::SCALE_CONTAIN && $resizeRatio < $sourceRatio
      ) {
        $height = $width / $sourceRatio;
        $offsetY = $resizeHeight / 2 - $height / 2;
      } elseif (
        $scale === Image::SCALE_COVER && $resizeRatio < $sourceRatio ||
        $scale === Image::SCALE_CONTAIN && $resizeRatio > $sourceRatio
      ) {
        $width = $height * $sourceRatio;
        $offsetX = $resizeWidth / 2 - $width / 2;
      }
    }

    $source = $this->parseImage($image);

    if ($background !== null) {
      imagefill($output, 0, 0, imagecolorallocatealpha(
        $source,
        (int)$background[0],
        (int)$background[1],
        (int)$background[2],
        (float)$background[3]
      ));
    }

    imagecopyresampled(
      $output,
      $source,
      $offsetX,
      $offsetY,
      0,
      0,
      $width,
      $height,
      $image->getWidth(),
      $image->getHeight()
    );

    $this->writeImage($output, $image->getPath(), $image->getFormat(), $quality);

    imagedestroy($source);
    imagedestroy($output);
  }

  public function rotate(Image $image, int $angle, ?array $background = null, float $quality = -1): void
  {
    $source = $this->parseImage($image);

    if ($background !== null) {
      $output = imagerotate($source, $angle, imagecolorallocatealpha(
        $source,
        (int)$background[0],
        (int)$background[1],
        (int)$background[2],
        (float)$background[3]
      ));
    } else {
      $output = imagerotate($source, $angle, 0);
    }

    $this->writeImage($output, $image->getPath(), $image->getFormat(), $quality);

    imagedestroy($source);
    imagedestroy($output);
  }

  public function convert(Image $image, string $dest, string $format = Image::FORMAT_JPG, ?array $background = null, int $quality = -1): void
  {
    $source = $this->parseImage($image);
    $output = imagecreatetruecolor($image->getWidth(), $image->getHeight());

    if ($background !== null) {
      imagefill($output, 0, 0, imagecolorallocatealpha(
        $source,
        (int)$background[0],
        (int)$background[1],
        (int)$background[2],
        (float)$background[3]
      ));
    }

    imagecopy($output, $source, 0, 0, 0, 0, $image->getWidth(), $image->getHeight());

    $this->writeImage($output, $dest, $format, $quality);

    imagedestroy($source);
    imagedestroy($output);
  }
}
