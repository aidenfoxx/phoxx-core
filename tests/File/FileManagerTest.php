<?php declare(strict_types=1);

namespace Phoxx\Core\File
{
  final class FileManagerTestHelper
  {
    public static $source;

    public static $sourceFormat;
    
    public static $sourceResource;

    public static $dest;
    
    public static $destFormat;

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
      self::$sourceFormat = null;
      self::$sourceResource = null;
      self::$dest = null;
      self::$destFormat = null;
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
    FileManagerTestHelper::$sourceFormat = 'jpg';
    FileManagerTestHelper::$sourceResource = imagecreate(1, 1);

    return FileManagerTestHelper::$sourceResource;
  }

  function imagecreatefrombmp($source)
  {
    FileManagerTestHelper::$source = $source;
    FileManagerTestHelper::$sourceFormat = 'bmp';
    FileManagerTestHelper::$sourceResource = imagecreate(1, 1);

    return FileManagerTestHelper::$sourceResource;
  }

  function imagecreatefrompng($source)
  {
    FileManagerTestHelper::$source = $source;
    FileManagerTestHelper::$sourceFormat = 'png';
    FileManagerTestHelper::$sourceResource = imagecreate(1, 1);

    return FileManagerTestHelper::$sourceResource;
  }

  function imagecreatefromgif($source)
  {
    FileManagerTestHelper::$source = $source;
    FileManagerTestHelper::$sourceFormat = 'gif';
    FileManagerTestHelper::$sourceResource = imagecreate(1, 1);

    return FileManagerTestHelper::$sourceResource;
  }

  function imagecreatefromwebp($source)
  {
    FileManagerTestHelper::$source = $source;
    FileManagerTestHelper::$sourceFormat = 'webp';
    FileManagerTestHelper::$sourceResource = imagecreate(1, 1);

    return FileManagerTestHelper::$sourceResource;
  }

  function imagejpeg($resource, $dest, $quality)
  {
    FileManagerTestHelper::$dest = $dest;
    FileManagerTestHelper::$destFormat = 'jpg';
    FileManagerTestHelper::$destResource = $resource;
    FileManagerTestHelper::$quality = $quality;

    return true;
  }

  function imagebmp($resource, $dest)
  {
    FileManagerTestHelper::$dest = $dest;
    FileManagerTestHelper::$destFormat = 'bmp';
    FileManagerTestHelper::$destResource = $resource;

    return true;
  }

  function imagepng($resource, $dest, $quality)
  {
    FileManagerTestHelper::$dest = $dest;
    FileManagerTestHelper::$destFormat = 'png';
    FileManagerTestHelper::$destResource = $resource;
    FileManagerTestHelper::$quality = $quality;

    return true;
  }

  function imagegif($resource, $dest)
  {
    FileManagerTestHelper::$dest = $dest;
    FileManagerTestHelper::$destFormat = 'gif';
    FileManagerTestHelper::$destResource = $resource;

    return true;
  }

  function imagewebp($resource, $dest, $quality)
  {
    FileManagerTestHelper::$dest = $dest;
    FileManagerTestHelper::$destFormat = 'webp';
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

    public function imageFormats(): array
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
      $fileManager = new FileManager();
      $fileManager->copy(new File(PATH_BASE . '/File/FileManagerTest/test.txt'), 'dest');
  
      $this->assertSame(realpath(PATH_BASE . '/File/FileManagerTest/test.txt'), FileManagerTestHelper::$source);
      $this->assertSame('dest', FileManagerTestHelper::$dest);
    }
  
    public function testShouldMoveFile()
    {
      $fileManager = new FileManager();
      $fileManager->move(new File(PATH_BASE . '/File/FileManagerTest/test.txt'), 'dest');
  
      $this->assertSame(realpath(PATH_BASE . '/File/FileManagerTest/test.txt'), FileManagerTestHelper::$source);
      $this->assertSame('dest', FileManagerTestHelper::$dest);
    }
  
    public function testShouldDeleteFile()
    {
      $fileManager = new FileManager();
      $fileManager->delete(new File(PATH_BASE . '/File/FileManagerTest/test.txt'));
  
      $this->assertSame(realpath(PATH_BASE . '/File/FileManagerTest/test.txt'), FileManagerTestHelper::$source);
    }

    public function testShouldRejectCopyInvalidFile()
    {
      FileManagerTestHelper::$success = false;

      $this->expectException(FileException::class);

      $fileManager = new FileManager();
      $fileManager->copy(new File(PATH_BASE . '/File/FileManagerTest/test.txt'), 'dest');
    }

    public function testShouldRejectMoveInvalidFile()
    {
      FileManagerTestHelper::$success = false;

      $this->expectException(FileException::class);
  
      $fileManager = new FileManager();
      $fileManager->move(new File(PATH_BASE . '/File/FileManagerTest/test.txt'), 'dest');
    }

    public function testShouldRejectDeleteInvalidFile()
    {
      FileManagerTestHelper::$success = false;

      $this->expectException(FileException::class);

      $fileManager = new FileManager();
      $fileManager->delete(new File(PATH_BASE . '/File/FileManagerTest/test.txt'));
    }

    public function testShouldRotateImage()
    {
      $fileManager = new FileManager();
      $fileManager->rotate(new Image(PATH_BASE . '/File/FileManagerTest/test.jpg'), 180);

      $this->assertSame(realpath(PATH_BASE . '/File/FileManagerTest/test.jpg'), FileManagerTestHelper::$source);
      $this->assertSame(realpath(PATH_BASE . '/File/FileManagerTest/test.jpg'), FileManagerTestHelper::$dest);

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
      $fileManager->rotate(new Image(PATH_BASE . '/File/FileManagerTest/test.jpg'), 180, [255, 255, 255, 1.0]);
  
      $this->assertSame(255, FileManagerTestHelper::$background);
    }

    public function testShouldRotateImageWithQuality()
    {  
      $fileManager = new FileManager();
      $fileManager->rotate(new Image(PATH_BASE . '/File/FileManagerTest/test.jpg'), 180, null, 50);
  
      $this->assertSame(50, FileManagerTestHelper::$quality);
    }

    public function testShouldResizeImage()
    {
      $fileManager = new FileManager();
      $fileManager->resize(new Image(PATH_BASE . '/File/FileManagerTest/test.jpg'), 32, 32);

      $this->assertSame(realpath(PATH_BASE . '/File/FileManagerTest/test.jpg'), FileManagerTestHelper::$source);
      $this->assertSame(realpath(PATH_BASE . '/File/FileManagerTest/test.jpg'), FileManagerTestHelper::$dest);

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
      $fileManager->resize(new Image(PATH_BASE . '/File/FileManagerTest/test.jpg'), $resizeWidth, $resizeHeight, $scale);

      $this->assertEquals($destWidth, FileManagerTestHelper::$destWidth);
      $this->assertEquals($destHeight, FileManagerTestHelper::$destHeight);
      $this->assertEquals($offsetX, FileManagerTestHelper::$offsetX);
      $this->assertEquals($offsetY, FileManagerTestHelper::$offsetY);
    }

    public function testShouldResizeImageWithBackground()
    {  
      $fileManager = new FileManager();
      $fileManager->resize(new Image(PATH_BASE . '/File/FileManagerTest/test.jpg'), 32, 32, Image::SCALE_FILL, [255, 255, 255, 1.0]);
  
      $this->assertSame(255, FileManagerTestHelper::$background);
    }

    public function testShouldResizeImageWithQuality()
    {  
      $fileManager = new FileManager();
      $fileManager->resize(new Image(PATH_BASE . '/File/FileManagerTest/test.jpg'), 32, 32, Image::SCALE_FILL, null, 50);
  
      $this->assertSame(50, FileManagerTestHelper::$quality);
    }

    public function testShouldConvertImage()
    {
      $fileManager = new FileManager();
      $fileManager->convert(new Image(PATH_BASE . '/File/FileManagerTest/test.jpg'), 'dest', Image::FORMAT_PNG);

      $this->assertSame(realpath(PATH_BASE . '/File/FileManagerTest/test.jpg'), FileManagerTestHelper::$source);
      $this->assertSame('dest', FileManagerTestHelper::$dest);

      $this->assertTrue(is_resource(FileManagerTestHelper::$sourceResource));
      $this->assertTrue(is_resource(FileManagerTestHelper::$destResource));
      $this->assertNotSame(FileManagerTestHelper::$sourceResource, FileManagerTestHelper::$destResource);

      $this->assertSame(
        [FileManagerTestHelper::$sourceResource, FileManagerTestHelper::$destResource],
        FileManagerTestHelper::$destroyed
      );
    }

    public function testShouldConvertImageWithBackground()
    {  
      $fileManager = new FileManager();
      $fileManager->convert(new Image(PATH_BASE . '/File/FileManagerTest/test.jpg'), 'dest', Image::FORMAT_PNG, [255, 255, 255, 1.0]);
  
      $this->assertSame(255, FileManagerTestHelper::$background);
    }

    public function testShouldConvertImageWithQuality()
    {
      $fileManager = new FileManager();
      $fileManager->convert(new Image(PATH_BASE . '/File/FileManagerTest/test.jpg'), 'dest', Image::FORMAT_PNG, null, 50);

      $this->assertSame(50, FileManagerTestHelper::$quality);
    }

    public function testShouldCompressImage()
    {
      $fileManager = new FileManager();
      $fileManager->compress(new Image(PATH_BASE . '/File/FileManagerTest/test.jpg'), 50);

      $this->assertSame(realpath(PATH_BASE . '/File/FileManagerTest/test.jpg'), FileManagerTestHelper::$source);
      $this->assertSame(realpath(PATH_BASE . '/File/FileManagerTest/test.jpg'), FileManagerTestHelper::$dest);

      $this->assertSame(50, FileManagerTestHelper::$quality);

      $this->assertTrue(is_resource(FileManagerTestHelper::$sourceResource));
      $this->assertTrue(is_resource(FileManagerTestHelper::$destResource));
      $this->assertSame(FileManagerTestHelper::$sourceResource, FileManagerTestHelper::$destResource);

      $this->assertSame([FileManagerTestHelper::$sourceResource], FileManagerTestHelper::$destroyed);
    }

    /**
     * @dataProvider imageFormats
     */
    public function testShouldHandleImageFormat($format)
    {
      $fileManager = new FileManager();
      $fileManager->compress(new Image(PATH_BASE . '/File/FileManagerTest/test.' . $format), -1);

      $this->assertSame($format, FileManagerTestHelper::$sourceFormat);
      $this->assertSame($format, FileManagerTestHelper::$destFormat);
    }
  }
}
