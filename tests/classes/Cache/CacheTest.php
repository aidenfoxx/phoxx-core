<?php declare(strict_types=1);

namespace Phoxx\Core\Tests\Cache;

use Phoxx\Core\Cache\Cache;
use Phoxx\Core\Cache\Interfaces\CacheDriver;

use PHPUnit\Framework\TestCase;

final class MockCacheDriver implements CacheDriver
{
  public function getValue(string $index)
  {
    return isset(CacheTest::$cache[$index]) === true ? CacheTest::$cache[$index] : null;
  }

  public function setValue(string $index, $value, int $lifetime = 0): void
  {
    CacheTest::$cache[$index] = [$value, $lifetime];
  }

  public function removeValue(string $index): void
  {
    unset(CacheTest::$cache[$index]);
  }

  public function clear(): void
  {
    CacheTest::$cache = [];
  }
}

final class CacheTest extends TestCase
{
  public static $cache = [];

  public function testGetValue()
  {
    $cache = new Cache(new MockCacheDriver());

    self::$cache['INDEX'] = ['VALUE', -1];

    $this->assertSame(['VALUE', -1], $cache->getValue('INDEX'));
  }

  public function testSetValue()
  {
    $cache = new Cache(new MockCacheDriver());
    $cache->setValue('INDEX', 'VALUE', -1);

    $this->assertSame(self::$cache['INDEX'], ['VALUE', -1]);
  }

  public function testRemoveValue()
  {
    $cache = new Cache(new MockCacheDriver());

    self::$cache['INDEX'] = ['VALUE', -1];

    $cache->removeValue('INDEX');

    $this->assertSame([], self::$cache);
  }

  public function testClear()
  {
    $cache = new Cache(new MockCacheDriver());

    self::$cache['INDEX'] = ['VALUE', -1];

    $cache->clear();

    $this->assertSame([], self::$cache);
  }

  public function testGetDriver()
  {
    $cacheDriver = new MockCacheDriver();
    $cache = new Cache($cacheDriver);

    $this->assertSame($cacheDriver, $cache->getDriver());
  }
}
