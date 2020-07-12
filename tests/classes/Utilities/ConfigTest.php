<?php declare(strict_types=1);

namespace Phoxx\Core\Tests\Utilities;

use Phoxx\Core\Cache\Cache;
use Phoxx\Core\Cache\Interfaces\CacheDriver;
use Phoxx\Core\File\Exceptions\FileException;
use Phoxx\Core\Utilities\Config;
use Phoxx\Core\Utilities\Exceptions\ConfigException;

use PHPUnit\Framework\TestCase;

final class MockCacheDriver implements CacheDriver
{
  public function getValue(string $index)
  {
    return isset(ConfigTest::$cache[$index]) === true ? ConfigTest::$cache[$index] : null;
  }

  public function setValue(string $index, $value, int $lifetime = 0): void
  {
    ConfigTest::$cache[$index] = $value;
  }

  public function removeValue(string $index): void
  {
    /**
     * Not used.
     */
  }

  public function clear(): void
  {
    /**
     * Not used.
     */
  }
}

final class ConfigTest extends TestCase
{
  public static $cache = [];

  public function fileExceptionsProvider(): array
  {
    return [
      ['INVALID', FileException::class],
      ['@INVALID/CONFIG', ConfigException::class],
    ];
  }

  public function setUp(): void
  {
    self::$cache = [];
  }

  public function testGetFile(): void
  {
    $config = new Config();
    $config->addPath('./config');
    $config->addPath('./config/namespace', 'namespace');

    $this->assertSame('VALUE', $config->getFile('config')->CONFIG);
    $this->assertSame('VALUE', $config->getFile('@namespace/config')->NAMESPACED);
  }

  public function testGetCachedFile(): void
  {
    $config = new Config(new Cache(new MockCacheDriver()));
    $config->addPath('./config');
    $configPath = realpath('./config/config.php');

    self::$cache[$configPath] = ['CONFIG' => 'VALUE'];

    $this->assertSame('VALUE', $config->getFile('config')->CONFIG);
  }

  public function testSetCachedFile(): void
  {
    $config = new Config(new Cache(new MockCacheDriver()));
    $config->addPath('./config');
    $configPath = realpath('./config/config.php');

    $this->assertSame('VALUE', $config->getFile('config')->CONFIG);
    $this->assertSame(['CONFIG' => 'VALUE'], self::$cache[$configPath]);
  }

  public function testAbsolutePath(): void
  {
    $config = new Config();
    $config->addPath(realpath('./config'));

    $this->assertSame('VALUE', $config->getFile('config')->CONFIG);
  }

  public function testAbsoluteBasePath(): void
  {
    $config = new Config(null, realpath(PATH_BASE));
    $config->addPath('./config');

    $this->assertSame('VALUE', $config->getFile('config')->CONFIG);
  }

  /**
   * @dataProvider fileExceptionsProvider
   */
  public function testGetFileExceptions(string $file, string $exception): void
  {
    $config = new Config();
    $config->addPath('./config');

    $this->expectException($exception);

    $config->getFile($file);
  }
}
