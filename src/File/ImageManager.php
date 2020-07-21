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
      throw new ImageException('Failed to parse image `' . $image->getPath() . '`.');
    }
  }

  public function compress(Image $image, float $quality = -1): void
  {
    $input = $this->parseImage($image);

    $this->writeImage($input, $image->getPath(), $image->getFormat(), $quality);

    imagedestroy($input);
  }

  public function resize(
    Image $image,
    int $width,
    int $height,
    string $scale = self::SCALE_FILL,
    ?string $background = null,
    float $quality = -1
  ): void {
    $offsetX = 0;
    $offsetY = 0;

    $input = $this->parseImage($image);
    $output = imagecreatetruecolor($width, $height);

    if ($width > 0 && $height > 0) {
      $resizeWidth = $width;
      $resizeHeight = $height;

      $resizeRatio = $width / $height;
      $sourceRatio = $image->getWidth() / $image->getHeight();

      switch ($scale) {
        case Image::SCALE_COVER:
          if ($resizeRatio > $sourceRatio) {
            $height = $width * $sourceRatio;
            $offsetY = $resizeHeight / 2 - $height / 2;
          } elseif ($resizeRatio < $sourceRatio) {
            $width = $height * $sourceRatio;
            $offsetX = $resizeWidth / 2 - $width / 2;
          }
          break;

        case Image::SCALE_CONTAIN:
          if ($background !== null) {
            $fill = imagecolorallocatealpha(
              $input,
              (int)$background[0],
              (int)$background[1],
              (int)$background[2],
              (float)$background[3]
            );

            imagefill($output, 0, 0, $fill);
          }

          if ($resizeRatio > $sourceRatio) {
            $width = $height * $sourceRatio;
            $offsetX = $resizeWidth / 2 - $width / 2;
          } elseif ($resizeRatio < $sourceRatio) {
            $height = $width / $sourceRatio;
            $offsetY = $resizeHeight / 2 - $height / 2;
          }
          break;
      }
    }

    imagecopyresampled(
      $output,
      $input,
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

    imagedestroy($input);
    imagedestroy($output);
  }

  public function rotate(Image $image, int $angle, ?array $background = null, float $quality = -1): void
  {
    $input = $this->parseImage($image);

    if ($background !== null) {
      $fill = imagecolorallocatealpha(
        $input,
        (int)$background[0],
        (int)$background[1],
        (int)$background[2],
        (float)$background[3]
      );

      $output = imagerotate($input, $angle, $fill);
    } else {
      $output = imagerotate($input, $angle, 0);
    }

    $this->writeImage($output, $image->getPath(), $image->getFormat(), $quality);

    imagedestroy($input);
    imagedestroy($output);
  }

  public function convert(
    Image $image,
    string $dest,
    string $format = self::FORMAT_JPG,
    ?array $background = null,
    int $quality = -1
  ): Image {
    $input = $this->parseImage($image);
    $output = imagecreatetruecolor($image->getWidth(), $image->getHeight());

    if ($background !== null) {
      $fill = imagecolorallocatealpha(
        $input,
        (int)$background[0],
        (int)$background[1],
        (int)$background[2],
        (float)$background[3]
      );

      imagefill($output, 0, 0, $fill);
    }

    imagecopy($output, $input, 0, 0, 0, 0, $image->getWidth(), $image->getHeight());

    $this->writeImage($output, $dest, $format, $quality);

    imagedestroy($input);
    imagedestroy($output);

    return new Image($dest);
  }
}
