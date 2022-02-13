<?php declare(strict_types=1);

namespace Phoxx\Core\Tests\Utilities;

use Phoxx\Core\Cache\Cache;
use Phoxx\Core\Exceptions\ConfigException;
use Phoxx\Core\Exceptions\FileException;
use Phoxx\Core\Utilities\Config;

use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
  public function testShouldGetConfig(): void
  {
    $config = new Config();
    $config->addPath('./System/ConfigTest');

    $this->assertSame('VALUE', $config->open('config')->KEY);
  }

  public function testShouldGetNamespacedConfig(): void
  {
    $config = new Config();
    $config->addPath('./System/ConfigTest', 'namespace');

    $this->assertSame('VALUE', $config->open('@namespace/config')->KEY);
  }

  public function testShouldSetCachedConfig(): void
  {
    $cache = $this->createMock(Cache::class);
    $cache->expects($this->once())
          ->method('setValue')
          ->with($this->equalTo(realpath(PATH_BASE . '/System/ConfigTest/config.php')), ['KEY' => 'VALUE']);

    $config = new Config($cache);
    $config->addPath('./System/ConfigTest');

    $this->assertSame('VALUE', $config->open('config')->KEY);
  }

  public function testShouldGetCachedConfig(): void
  {
    $cache = $this->createMock(Cache::class);
    $cache->expects($this->once())
          ->method('getValue')
          ->with($this->equalTo(realpath(PATH_BASE . '/System/ConfigTest/config.php')))
          ->willReturn(['KEY' => 'VALUE']);

    $config = new Config($cache);
    $config->addPath('./System/ConfigTest');

    $this->assertSame('VALUE', $config->open('config')->KEY);
  }

  public function testShouldUseBasePath(): void
  {
    $config = new Config(null, __DIR__);
    $config->addPath('./ConfigTest');

    $this->assertSame('TEST', $config->open('config')->CONFIG);
  }

  public function testShouldRejectInvalidConfig(string $file, string $exception): void
  {
    $config = new Config();
    $config->addPath('./System/ConfigTest');

    $this->expectException(FileException:class);

    $config->open('invalid');
  }

  public function testShouldRejectInvalidNamespace(string $file, string $exception): void
  {
    $config = new Config();
    $config->addPath('./System/ConfigTest');

    $this->expectException(ConfigException:class);

    $config->open('config', 'invalid');
  }

  public function testShouldRejectAbsolutePaths(string $basePath, string $path): void
  {
    $config = new Config();
    $config->addPath('./System/ConfigTest');

    $this->expectException(ConfigException:class);

    $config->open(realpath(PATH_BASE . '/System/ConfigTest/config.php'));
  }
}
