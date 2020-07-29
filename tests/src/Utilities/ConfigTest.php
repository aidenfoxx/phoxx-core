<?php

declare(strict_types=1);

namespace Phoxx\Core\Tests\Utilities;

use Phoxx\Core\Cache\Cache;
use Phoxx\Core\Cache\Interfaces\CacheDriver;
use Phoxx\Core\File\Exceptions\FileException;
use Phoxx\Core\Utilities\Config;
use Phoxx\Core\Utilities\Exceptions\ConfigException;

use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
  public function absolutePathProvider(): array
  {
    return [
      [realpath(PATH_BASE), realpath(PATH_BASE . '/config')],
      [realpath(PATH_BASE), './config'],
      [PATH_BASE, realpath(PATH_BASE . '/config')]
    ];
  }

  public function fileExceptionsProvider(): array
  {
    return [
      ['INVALID', FileException::class],
      ['@INVALID/CONFIG', ConfigException::class],
      ['C:/CONFIG', ConfigException::class],
      ['/CONFIG', ConfigException::class]
    ];
  }

  public function testOpenConfig(): void
  {
    $config = new Config();
    $config->addPath('./config');
    $config->addPath('./config/namespace', 'namespace');

    $this->assertSame('VALUE', $config->open('config')->CONFIG);
    $this->assertSame('VALUE', $config->open('@namespace/config')->NAMESPACED);
  }

  public function testOpenCachedConfig(): void
  {
    $mockDriver = $this->createMock(CacheDriver::class);
    $mockDriver->expects($this->once())
               ->method('getValue')
               ->with($this->equalTo(realpath(PATH_BASE . '/config/config.php')))
               ->willReturn(['CONFIG' => 'VALUE']);

    $config = new Config(new Cache($mockDriver));
    $config->addPath('./config');

    $this->assertSame('VALUE', $config->open('config')->CONFIG);
  }

  public function testSetCachedConfig(): void
  {
    $mockDriver = $this->createMock(CacheDriver::class);
    $mockDriver->expects($this->once())
               ->method('setValue')
               ->with($this->equalTo(realpath(PATH_BASE . '/config/config.php')), ['CONFIG' => 'VALUE']);

    $config = new Config(new Cache($mockDriver));
    $config->addPath('./config');

    $this->assertSame('VALUE', $config->open('config')->CONFIG);
  }

  /**
   * @dataProvider absolutePathProvider
   */
  public function testAbsolutePaths(string $basePath, string $path): void
  {
    $config = new Config(null, $basePath);
    $config->addPath($path);

    $this->assertSame('VALUE', $config->open('config')->CONFIG);
  }

  /**
   * @dataProvider fileExceptionsProvider
   */
  public function testExceptions(string $file, string $exception): void
  {
    $config = new Config();
    $config->addPath('./config');

    $this->expectException($exception);

    $config->open($file);
  }
}
