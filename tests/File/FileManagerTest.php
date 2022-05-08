<?php declare(strict_types=1);

namespace Phoxx\Core\File
{
  final class FileManagerTestHelper
  {
    public static $source;

    public static $dest;

    public static $success = true;

    public static function clear()
    {
      self::$source = null;
      self::$dest = null;
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
}

namespace Phoxx\Core\Tests\File
{
  use Phoxx\Core\File\File;
  use Phoxx\Core\File\FileManager;
  use Phoxx\Core\File\FileManagerTestHelper;
  use Phoxx\Core\Exceptions\FileException;

  use PHPUnit\Framework\TestCase;
  
  final class FileManagerTest extends TestCase
  {
    public function setUp(): void
    {
      FileManagerTestHelper::clear();
    }

    public function testShouldCopyFile()
    {
      $fileManager = new FileManager();
      $fileManager->copy(new File(PATH_BASE . '/File/FileTest/test.txt'), 'dest');
  
      $this->assertSame(FileManagerTestHelper::$source, realpath(PATH_BASE . '/File/FileTest/test.txt'));
      $this->assertSame(FileManagerTestHelper::$dest, 'dest');
    }
  
    public function testShouldMoveFile()
    {
      $fileManager = new FileManager();
      $fileManager->move(new File(PATH_BASE . '/File/FileTest/test.txt'), 'dest');
  
      $this->assertSame(FileManagerTestHelper::$source, realpath(PATH_BASE . '/File/FileTest/test.txt'));
      $this->assertSame(FileManagerTestHelper::$dest, 'dest');
    }
  
    public function testShouldDeleteFile()
    {
      $fileManager = new FileManager();
      $fileManager->delete(new File(PATH_BASE . '/File/FileTest/test.txt'));
  
      $this->assertSame(FileManagerTestHelper::$source, realpath(PATH_BASE . '/File/FileTest/test.txt'));
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
  }
}
