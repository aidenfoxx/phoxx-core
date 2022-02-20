<?php declare(strict_types=1);

namespace Phoxx\Core\Tests\Utilities;

use Phoxx\Core\Cache\Cache;
use Phoxx\Core\Exceptions\ConfigException;
use Phoxx\Core\Exceptions\FileException;
use Phoxx\Core\System\Config;

use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
  public function testShouldGetConfig(): void
  {
    $config = new Config();
    $config->addPath('./System/ConfigTest');

    $this->assertSame('VALUE', $config->open('config')->CONFIG);
  }

  public function testShouldGetNamespacedConfig(): void
  {
    $config = new Config();
    $config->addPath('./System/ConfigTest', 'namespace');

    $this->assertSame('VALUE', $config->open('@namespace/config')->CONFIG);
  }

  public function testShouldGetAbsoluteConfig(): void
  {
      $config = new Config();
      $config->addPath(realpath(PATH_BASE) . '/System/ConfigTest');

      $this->assertSame('VALUE', $config->open('config')->CONFIG);
  }

  public function testShouldSetCachedConfig(): void
  {
    $cache = $this->createMock(Cache::class);
    $cache->expects($this->once())
          ->method('setValue')
          ->with($this->equalTo(realpath(PATH_BASE . '/System/ConfigTest/config.php')), ['CONFIG' => 'VALUE']);

    $config = new Config($cache);
    $config->addPath('./System/ConfigTest');

    $this->assertSame('VALUE', $config->open('config')->CONFIG);
  }

  public function testShouldGetCachedConfig(): void
  {
    $cache = $this->createMock(Cache::class);
    $cache->expects($this->once())
          ->method('getValue')
          ->with($this->equalTo(realpath(PATH_BASE . '/System/ConfigTest/config.php')))
          ->willReturn(['CONFIG' => 'VALUE']);

    $config = new Config($cache);
    $config->addPath('./System/ConfigTest');

    $this->assertSame('VALUE', $config->open('config')->CONFIG);
  }

  public function testShouldRejectInvalidConfig(): void
  {
    $config = new Config();
    $config->addPath('./System/ConfigTest');

    $this->expectException(FileException::class);

    $config->open('invalid');
  }

  public function testShouldRejectInvalidNamespace(): void
  {
    $config = new Config();
    $config->addPath('./System/ConfigTest');

    $this->expectException(ConfigException::class);

    $config->open('@invalid/config');
  }

  public function testShouldRejectAbsoluteConfig(): void
  {
    $config = new Config();
    $config->addPath('./System/ConfigTest');

    $this->expectException(ConfigException::class);

    $config->open(realpath(PATH_BASE) . '/System/ConfigTest/config.php');
  }
}
