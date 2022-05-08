<?php declare(strict_types=1);

namespace
{
  final class Memcached
  {
    const MEMCACHED_SUCCESS = true;

    public static $host;

    public static $port;

    public static $weight;

    public static $index;

    public static $value;
    
    public static $lifetime;

    public static $success = true;

    public static $clear;

    public static function clear()
    {
      self::$host = null;
      self::$port = null;
      self::$weight = null;
      self::$index = null;
      self::$value = null;
      self::$lifetime = null;
      self::$success = true;
      self::$clear = null;
    }

    public function addServer($host, $port, $weight)
    {
      self::$host = $host;
      self::$port = $port;
      self::$weight = $weight;
    }

    public function get($index)
    {
      self::$index = $index;

      return self::$value;
    }

    public function getResultCode()
    {
      return self::$success;
    }

    public function set($index, $value, $lifetime)
    {
      self::$index = $index;
      self::$value = $value;
      self::$lifetime = $lifetime;
    }

    public function delete($index)
    {
      self::$index = $index;
    }

    public function flush()
    {
      self::$clear = true;
    }
  }
}

namespace Phoxx\Core\Tests\Cache\Drivers
{
  use Memcached;

  use Phoxx\Core\Cache\Drivers\MemcachedDriver;
  
  use PHPUnit\Framework\TestCase;
  
  final class MemcachedDriverTest extends TestCase
  {
    public function setUp(): void
    {
      Memcached::clear();
    }

    public function testShouldGetMemcached()
    {  
      $driver = new MemcachedDriver();
  
      $this->assertInstanceOf(Memcached::class, $driver->getMemcached());
    }

    public function testShouldAddServer()
    {
      $driver = new MemcachedDriver();
      $driver->addServer('host', 11211, 100);

      $this->assertSame('host', Memcached::$host);
      $this->assertSame(11211, Memcached::$port);
      $this->assertSame(100, Memcached::$weight);
    }

    public function testShouldGetValue()
    {
      Memcached::$value = 'value';
  
      $driver = new MemcachedDriver();
  
      $this->assertSame('value', $driver->getValue('index'));
      $this->assertSame('index', Memcached::$index);
    }

    public function testShouldGetValueNullOnSuccessFalse()
    {
      Memcached::$value = 'value';
      Memcached::$success = false;
  
      $driver = new MemcachedDriver();
  
      $this->assertNull($driver->getValue('index'));
    }

    public function testShouldSetValue()
    {  
      $driver = new MemcachedDriver();
      $driver->setValue('index', 'value');
  
      $this->assertSame('index', Memcached::$index);
      $this->assertSame('value', Memcached::$value);
      $this->assertSame(0, Memcached::$lifetime);
    }

    public function testShouldSetValueWithLifetime()
    {  
      $driver = new MemcachedDriver();
      $driver->setValue('index', 'value', 10);
  
      $this->assertSame('index', Memcached::$index);
      $this->assertSame('value', Memcached::$value);
      $this->assertSame(10, Memcached::$lifetime);
    }

    public function testShouldRemoveValue()
    {  
      $driver = new MemcachedDriver();
      $driver->removeValue('index');
  
      $this->assertSame('index', Memcached::$index);
    }

    public function testShouldClear()
    {  
      $driver = new MemcachedDriver();
      $driver->clear();
  
      $this->assertTrue(Memcached::$clear);
    }
  }
}