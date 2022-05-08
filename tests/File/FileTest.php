<?php declare(strict_types=1);

namespace Phoxx\Core\Tests\File;

use Phoxx\Core\File\File;
use Phoxx\Core\Exceptions\FileException;

use PHPUnit\Framework\TestCase;

final class FileTest extends TestCase
{
  public function testShouldCreateFile()
  {
    $file = new File(PATH_BASE . '/File/FileTest/test.txt');
  
    $this->assertSame(realpath(PATH_BASE . '/File/FileTest/test.txt'), $file->getPath());
    $this->assertSame('test', $file->getName());
    $this->assertSame('test.txt', $file->getBaseName());
    $this->assertSame(realpath(PATH_BASE . '/File/FileTest'), $file->getDirectory());
    $this->assertSame('txt', $file->getExtension());
    $this->assertSame('inode/x-empty', $file->getMimetype());
  }

  public function testShouldRejectInvalidFile()
  {
    $this->expectException(FileException::class);

    new File('invalid');
  }
}
