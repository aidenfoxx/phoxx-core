<?php declare(strict_types=1);

namespace Phoxx\Core\Tests\File;

use Phoxx\Core\Exceptions\ImageException;
use Phoxx\Core\File\Image;

use PHPUnit\Framework\TestCase;

final class ImageTest extends TestCase
{
  public function testShouldCreateJpgImage()
  {
    $image = new Image(PATH_BASE . '/File/ImageTest/test.jpg');
  
    $this->assertSame(Image::FORMAT_JPG, $image->getFormat());
    $this->assertSame(8, $image->getWidth());
    $this->assertSame(16, $image->getHeight());
  }

  public function testShouldCreatePngImage()
  {
    $image = new Image(PATH_BASE . '/File/ImageTest/test.png');
  
    $this->assertSame(Image::FORMAT_PNG, $image->getFormat());
    $this->assertSame(8, $image->getWidth());
    $this->assertSame(16, $image->getHeight());
  }

  public function testShouldCreateGifImage()
  {
    $image = new Image(PATH_BASE . '/File/ImageTest/test.gif');
  
    $this->assertSame(Image::FORMAT_GIF, $image->getFormat());
    $this->assertSame(8, $image->getWidth());
    $this->assertSame(16, $image->getHeight());
  }

  public function testShouldCreateBmpImage()
  {
    $image = new Image(PATH_BASE . '/File/ImageTest/test.bmp');
  
    $this->assertSame(Image::FORMAT_BMP, $image->getFormat());
    $this->assertSame(8, $image->getWidth());
    $this->assertSame(16, $image->getHeight());
  }

  public function testShouldRejectInvalidFormat()
  {
    $this->expectException(ImageException::class);

    $image = new Image(PATH_BASE . '/File/ImageTest/invalid.tif');
  }
}
