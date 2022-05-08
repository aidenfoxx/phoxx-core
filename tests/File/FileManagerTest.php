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

  function imagebmp($resource, $dest, $quality)
  {
    FileManagerTestHelper::$dest = $dest;
    FileManagerTestHelper::$destExtension = 'bmp';
    FileManagerTestHelper::$destResource = $resource;
    FileManagerTestHelper::$quality = $quality;

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

  function imagegif($resource, $dest, $quality)
  {
    FileManagerTestHelper::$dest = $dest;
    FileManagerTestHelper::$destExtension = 'gif';
    FileManagerTestHelper::$destResource = $resource;
    FileManagerTestHelper::$quality = $quality;

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

  function imagedestroy($resource)
  {
    FileManagerTestHelper::$destroyed[] = $resource;
  }
}

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
      $source = realpath(PATH_BASE . '/File/FileTest/test.txt');

      $fileManager = new FileManager();
      $fileManager->copy(new File(PATH_BASE . '/File/FileTest/test.txt'), 'dest');
  
      $this->assertSame($source, FileManagerTestHelper::$source);
      $this->assertSame('dest', FileManagerTestHelper::$dest);
    }
  
    public function testShouldMoveFile()
    {
      $source = realpath(PATH_BASE . '/File/FileTest/test.txt');

      $fileManager = new FileManager();
      $fileManager->move(new File(PATH_BASE . '/File/FileTest/test.txt'), 'dest');
  
      $this->assertSame($source, FileManagerTestHelper::$source);
      $this->assertSame('dest', FileManagerTestHelper::$dest);
    }
  
    public function testShouldDeleteFile()
    {
      $source = realpath(PATH_BASE . '/File/FileTest/test.txt');

      $fileManager = new FileManager();
      $fileManager->delete(new File($source));
  
      $this->assertSame($source, FileManagerTestHelper::$source);
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
      $source = realpath(PATH_BASE . '/File/ImageTest/test.jpg');

      $fileManager = new FileManager();
      $fileManager->rotate(new Image($source), 180);

      $this->assertSame($source, FileManagerTestHelper::$source);
      $this->assertSame($source, FileManagerTestHelper::$dest);

      $this->assertSame(180, FileManagerTestHelper::$angle);
      $this->assertSame(0, FileManagerTestHelper::$background);
      $this->assertSame(-1, FileManagerTestHelper::$quality);

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

    /**
     * @dataProvider imageExtensions
     */
    public function testShouldHandleImageExtension($extension)
    {
      $fileManager = new FileManager();
      $fileManager->rotate(new Image(PATH_BASE . '/File/ImageTest/test.' . $extension), 180);

      $this->assertSame($extension, FileManagerTestHelper::$sourceExtension);
      $this->assertSame($extension, FileManagerTestHelper::$destExtension);
    }
  }
}
