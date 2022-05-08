<?php declare(strict_types=1);

namespace Phoxx\Core\File
{
  final class FileManagerTestHelper
  {
    public static $source;

    public static $sourceExtension;
    
    public static $sourceResource;

    public static $dest;
    
    public static $destExtension;

    public static $destResource;
    
    public static $quality;

    public static $angle;

    public static $offsetX;

    public static $offsetY;

    public static $destWidth;

    public static $destHeight;

    public static $sourceWidth;

    public static $sourceHeight;

    public static $background;

    public static $destroyed = [];
    
    public static $success = true;
  
    public static function clear()
    {
      self::$source = null;
      self::$sourceExtension = null;
      self::$sourceResource = null;
      self::$dest = null;
      self::$destExtension = null;
      self::$destResource = null;
      self::$quality = null;
      self::$angle = null;
      self::$background = null;
      self::$destroyed = [];
      self::$success = true;
    }
  }

  function copy($source, $dest)
  {
    FileManagerTestHelper::$source = $source;
    FileManagerTestHelper::$dest = $dest;

    return FileManagerTestHelper::$success;
  }

  function rename($source, $dest)
  {
    FileManagerTestHelper::$source = $source;
    FileManagerTestHelper::$dest = $dest;

    return FileManagerTestHelper::$success;
  }

  function unlink($source)
  {
    FileManagerTestHelper::$source = $source;

    return FileManagerTestHelper::$success;
  }

  function imagecreatefromjpeg($source)
  {
    FileManagerTestHelper::$source = $source;
    FileManagerTestHelper::$sourceExtension = 'jpg';
    FileManagerTestHelper::$sourceResource = imagecreate(1, 1);

    return FileManagerTestHelper::$sourceResource;
  }

  function imagecreatefrombmp($source)
  {
    FileManagerTestHelper::$source = $source;
    FileManagerTestHelper::$sourceExtension = 'bmp';
    FileManagerTestHelper::$sourceResource = imagecreate(1, 1);

    return FileManagerTestHelper::$sourceResource;
  }

  function imagecreatefrompng($source)
  {
    FileManagerTestHelper::$source = $source;
    FileManagerTestHelper::$sourceExtension = 'png';
    FileManagerTestHelper::$sourceResource = imagecreate(1, 1);

    return FileManagerTestHelper::$sourceResource;
  }

  function imagecreatefromgif($source)
  {
    FileManagerTestHelper::$source = $source;
    FileManagerTestHelper::$sourceExtension = 'gif';
    FileManagerTestHelper::$sourceResource = imagecreate(1, 1);

    return FileManagerTestHelper::$sourceResource;
  }

  function imagecreatefromwebp($source)
  {
    FileManagerTestHelper::$source = $source;
    FileManagerTestHelper::$sourceExtension = 'webp';
    FileManagerTestHelper::$sourceResource = imagecreate(1, 1);

    return FileManagerTestHelper::$sourceResource;
  }

  function imagejpeg($resource, $dest, $quality)
  {
    FileManagerTestHelper::$dest = $dest;
    FileManagerTestHelper::$destExtension = 'jpg';
    FileManagerTestHelper::$destResource = $resource;
    FileManagerTestHelper::$quality = $quality;

    return true;
  }

  function imagebmp($resource, $dest)
  {
    FileManagerTestHelper::$dest = $dest;
    FileManagerTestHelper::$destExtension = 'bmp';
    FileManagerTestHelper::$destResource = $resource;

    return true;
  }

  function imagepng($resource, $dest, $quality)
  {
    FileManagerTestHelper::$dest = $dest;
    FileManagerTestHelper::$destExtension = 'png';
    FileManagerTestHelper::$destResource = $resource;
    FileManagerTestHelper::$quality = $quality;

    return true;
  }

  function imagegif($resource, $dest)
  {
    FileManagerTestHelper::$dest = $dest;
    FileManagerTestHelper::$destExtension = 'gif';
    FileManagerTestHelper::$destResource = $resource;

    return true;
  }

  function imagewebp($resource, $dest, $quality)
  {
    FileManagerTestHelper::$dest = $dest;
    FileManagerTestHelper::$destExtension = 'webp';
    FileManagerTestHelper::$destResource = $resource;
    FileManagerTestHelper::$quality = $quality;

    return true;
  }

  function imagerotate($resource, $angle, $background)
  {
    FileManagerTestHelper::$angle = $angle;
    FileManagerTestHelper::$background = $background;

    return imagecreate(1, 1);
  }

  function imagecolorallocatealpha()
  {
    return 255;
  }

  function imagecreatetruecolor()
  {
    return imagecreate(1, 1);
  }

  function imagefill($_resource, $_startX, $_startY, $background)
  {
    FileManagerTestHelper::$background = $background;
  }

  function imagecopyresampled(
    $dest,
    $source,
    $offsetX,
    $offsetY,
    $_sourceX,
    $_sourceY,
    $destWidth,
    $destHeight,
    $sourceWidth,
    $sourceHeight
  ) {
    FileManagerTestHelper::$destResource = $dest;
    FileManagerTestHelper::$sourceResource = $source;
    FileManagerTestHelper::$offsetX = $offsetX;
    FileManagerTestHelper::$offsetY = $offsetY;
    FileManagerTestHelper::$destWidth = $destWidth;
    FileManagerTestHelper::$destHeight = $destHeight;
    FileManagerTestHelper::$sourceWidth = $sourceWidth;
    FileManagerTestHelper::$sourceHeight = $sourceHeight;
  }

  function imagedestroy($resource)
  {
    FileManagerTestHelper::$destroyed[] = $resource;
  }
}

/**
 * TODO: This file needs cleaning up. Unify the source/dest naming with the
 * FileManager. Pick whatever works best for the project.
 * 
 * TODO: Add quality tests. We already have the var from writeImage.
 */
namespace Phoxx\Core\Tests\File
{
  use Phoxx\Core\File\File;
  use Phoxx\Core\File\Image;
  use Phoxx\Core\File\FileManager;
  use Phoxx\Core\File\FileManagerTestHelper;
  use Phoxx\Core\Exceptions\FileException;

  use PHPUnit\Framework\TestCase;
  
  final class FileManagerTest extends TestCase
  {
    public function resizeScaling(): array
    {
      return [
        [Image::SCALE_COVER, 32, 32, 32, 64, 0, -16],
        [Image::SCALE_COVER, 16, 64, 32, 64, -8, 0],
        [Image::SCALE_CONTAIN, 32, 32, 16, 32, 8, 0],
        [Image::SCALE_CONTAIN, 16, 64, 16, 32, 0, 16]
      ];
    }

    public function imageExtensions(): array
    {
      return [
        ['jpg'],
        ['bmp'],
        ['png'],
        ['gif'],
        ['webp']
      ];
    }

    public function setUp(): void
    {
      FileManagerTestHelper::clear();
    }

    public function testShouldCopyFile()
    {
      $source = new File(PATH_BASE . '/File/FileTest/test.txt');

      $fileManager = new FileManager();
      $fileManager->copy(new File(PATH_BASE . '/File/FileTest/test.txt'), 'dest');
  
      $this->assertSame($source->getPath(), FileManagerTestHelper::$source);
      $this->assertSame('dest', FileManagerTestHelper::$dest);
    }
  
    public function testShouldMoveFile()
    {
      $source = new File(PATH_BASE . '/File/FileTest/test.txt');

      $fileManager = new FileManager();
      $fileManager->move($source, 'dest'); // TODO: Should this be a variable?
  
      $this->assertSame($source->getPath(), FileManagerTestHelper::$source);
      $this->assertSame('dest', FileManagerTestHelper::$dest);
    }
  
    public function testShouldDeleteFile()
    {
      $source = new File(PATH_BASE . '/File/FileTest/test.txt');

      $fileManager = new FileManager();
      $fileManager->delete($source);
  
      $this->assertSame($source->getPath(), FileManagerTestHelper::$source);
    }

    public function testShouldRejectCopyInvalidFile()
    {
      FileManagerTestHelper::$success = false;

      $this->expectException(FileException::class);

      $fileManager = new FileManager();
      $fileManager->copy(new File(PATH_BASE . '/File/FileTest/test.txt'), 'dest');
    }

    public function testShouldRejectMoveInvalidFile()
    {
      FileManagerTestHelper::$success = false;

      $this->expectException(FileException::class);
  
      $fileManager = new FileManager();
      $fileManager->move(new File(PATH_BASE . '/File/FileTest/test.txt'), 'dest');
    }

    public function testShouldRejectDeleteInvalidFile()
    {
      FileManagerTestHelper::$success = false;

      $this->expectException(FileException::class);

      $fileManager = new FileManager();
      $fileManager->delete(new File(PATH_BASE . '/File/FileTest/test.txt'));
    }

    public function testShouldRotateImage()
    {
      $source = new Image(PATH_BASE . '/File/ImageTest/test.jpg');

      $fileManager = new FileManager();
      $fileManager->rotate($source, 180);

      $this->assertSame($source->getPath(), FileManagerTestHelper::$source);
      $this->assertSame($source->getPath(), FileManagerTestHelper::$dest);

      $this->assertSame(180, FileManagerTestHelper::$angle);
      $this->assertSame(0, FileManagerTestHelper::$background);

      $this->assertTrue(is_resource(FileManagerTestHelper::$sourceResource));
      $this->assertTrue(is_resource(FileManagerTestHelper::$destResource));
      $this->assertNotSame(FileManagerTestHelper::$sourceResource, FileManagerTestHelper::$destResource);

      $this->assertSame(
        [FileManagerTestHelper::$sourceResource, FileManagerTestHelper::$destResource],
        FileManagerTestHelper::$destroyed
      );
    }

    public function testShouldRotateImageWithBackground()
    {  
      $fileManager = new FileManager();
      $fileManager->rotate(new Image(PATH_BASE . '/File/ImageTest/test.jpg'), 180, [255, 255, 255, 1.0]);
  
      $this->assertSame(255, FileManagerTestHelper::$background);
    }

    public function testShouldRotateImageWithQuality()
    {  
      $fileManager = new FileManager();
      $fileManager->rotate(new Image(PATH_BASE . '/File/ImageTest/test.jpg'), 180, null, 50);
  
      $this->assertSame(50, FileManagerTestHelper::$quality);
    }

    public function testShouldResizeImage()
    {
      $source = new Image(PATH_BASE . '/File/ImageTest/test.jpg');

      $fileManager = new FileManager();
      $fileManager->resize($source, 32, 32);

      $this->assertSame($source->getPatH(), FileManagerTestHelper::$source);
      $this->assertSame($source->getPatH(), FileManagerTestHelper::$dest);

      $this->assertSame(8, FileManagerTestHelper::$sourceWidth);
      $this->assertSame(16, FileManagerTestHelper::$sourceHeight);
      $this->assertEquals(32, FileManagerTestHelper::$destWidth);
      $this->assertEquals(32, FileManagerTestHelper::$destHeight);

      $this->assertTrue(is_resource(FileManagerTestHelper::$sourceResource));
      $this->assertTrue(is_resource(FileManagerTestHelper::$destResource));
      $this->assertNotSame(FileManagerTestHelper::$sourceResource, FileManagerTestHelper::$destResource);

      $this->assertSame(
        [FileManagerTestHelper::$sourceResource, FileManagerTestHelper::$destResource],
        FileManagerTestHelper::$destroyed
      );
    }

    /**
     * @dataProvider resizeScaling
     */
    public function testShouldResizeImageWithScale($scale, $resizeWidth, $resizeHeight, $destWidth, $destHeight, $offsetX, $offsetY)
    {
      $fileManager = new FileManager();
      $fileManager->resize(new Image(PATH_BASE . '/File/ImageTest/test.jpg'), $resizeWidth, $resizeHeight, $scale);

      $this->assertEquals($destWidth, FileManagerTestHelper::$destWidth);
      $this->assertEquals($destHeight, FileManagerTestHelper::$destHeight);
      $this->assertEquals($offsetX, FileManagerTestHelper::$offsetX);
      $this->assertEquals($offsetY, FileManagerTestHelper::$offsetY);
    }

    public function testShouldResizeImageWithBackground()
    {  
      $fileManager = new FileManager();
      $fileManager->resize(new Image(PATH_BASE . '/File/ImageTest/test.jpg'), 32, 32, Image::SCALE_FILL, [255, 255, 255, 1.0]);
  
      $this->assertSame(255, FileManagerTestHelper::$background);
    }

    public function testShouldResizeImageWithQuality()
    {  
      $fileManager = new FileManager();
      $fileManager->resize(new Image(PATH_BASE . '/File/ImageTest/test.jpg'), 32, 32, Image::SCALE_FILL, null, 50);
  
      $this->assertSame(50, FileManagerTestHelper::$quality);
    }

    public function testShouldConvertImage()
    {
      $source = new Image(PATH_BASE . '/File/ImageTest/test.jpg');
      $dest = PATH_BASE . '/File/ImageTest/output.png';

      $fileManager = new FileManager();
      $fileManager->convert($source, $dest, Image::FORMAT_PNG);

      $this->assertSame($source->getPath(), FileManagerTestHelper::$source);
      $this->assertSame($dest, FileManagerTestHelper::$dest);

      $this->assertTrue(is_resource(FileManagerTestHelper::$sourceResource));
      $this->assertTrue(is_resource(FileManagerTestHelper::$destResource));
      $this->assertNotSame(FileManagerTestHelper::$sourceResource, FileManagerTestHelper::$destResource);

      $this->assertSame(
        [FileManagerTestHelper::$sourceResource, FileManagerTestHelper::$destResource],
        FileManagerTestHelper::$destroyed
      );
    }

    public function testShouldConvertImageWithQuality()
    {
      $source = PATH_BASE . '/File/ImageTest/test.jpg';

      $fileManager = new FileManager();
      $fileManager->convert(new Image($source), $source, Image::FORMAT_PNG, null, 50);

      $this->assertSame(50, FileManagerTestHelper::$quality);
    }

    public function testShouldCompressImage()
    {
      $source = realpath(PATH_BASE . '/File/ImageTest/test.jpg');

      $fileManager = new FileManager();
      $fileManager->compress(new Image($source), 50);

      $this->assertSame($source, FileManagerTestHelper::$source);
      $this->assertSame($source, FileManagerTestHelper::$dest);

      $this->assertSame(50, FileManagerTestHelper::$quality);

      $this->assertTrue(is_resource(FileManagerTestHelper::$sourceResource));
      $this->assertTrue(is_resource(FileManagerTestHelper::$destResource));
      $this->assertSame(FileManagerTestHelper::$sourceResource, FileManagerTestHelper::$destResource);

      $this->assertSame([FileManagerTestHelper::$sourceResource], FileManagerTestHelper::$destroyed);
    }

    /**
     * @dataProvider imageExtensions
     */
    public function testShouldHandleImageExtension($extension)
    {
      $fileManager = new FileManager();
      $fileManager->compress(new Image(PATH_BASE . '/File/ImageTest/test.' . $extension), -1);

      $this->assertSame($extension, FileManagerTestHelper::$sourceExtension);
      $this->assertSame($extension, FileManagerTestHelper::$destExtension);
    }
  }
}
