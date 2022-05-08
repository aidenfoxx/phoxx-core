<?php declare(strict_types=1);

namespace
{
  final class Redis
  {
    const MEMCACHED_SUCCESS = true;

    public static $host;

    public static $port;

    public static $index;

    public static $value;
    
    public static $lifetime;

    public static $success = true;

    public static $clear;

    public static function clear()
    {
      self::$host = null;
      self::$port = null;
      self::$index = null;
      self::$value = null;
      self::$lifetime = null;
      self::$success = true;
      self::$clear = null;
    }

    public function connect($host, $port)
    {
      self::$host = $host;
      self::$port = $port;
    }

    public function get($index)
    {
      self::$index = $index;

      return self::$value;
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

    public function flushAll()
    {
      self::$clear = true;
    }
  }
}

namespace Phoxx\Core\Tests\Cache\Drivers
{
  use Redis;

  use Phoxx\Core\Cache\Drivers\RedisDriver;
  
  use PHPUnit\Framework\TestCase;
  
  final class RedisDriverTest extends TestCase
  {
    public function setUp(): void
    {
      Redis::clear();
    }

    public function testShouldGetRedis()
    {  
      $driver = new RedisDriver('host', 6379);
  
      $this->assertInstanceOf(Redis::class, $driver->getRedis());
    }

    public function testShouldConnect()
    {
      $driver = new RedisDriver('host', 6379);

      $this->assertSame('host', Redis::$host);
      $this->assertSame(6379, Redis::$port);
    }

    public function testShouldGetValue()
    {
      Redis::$value = serialize('value');
  
      $driver = new RedisDriver('host', 6379);
  
      $this->assertSame('value', $driver->getValue('index'));
      $this->assertSame('index', Redis::$index);
    }

    public function testShouldGetValueFalse()
    {
      Redis::$value = serialize(false);
  
      $driver = new RedisDriver('host', 6379);
  
      $this->assertFalse($driver->getValue('index'));
    }

    public function testShouldGetValueNullOnSuccessFalse()
    {
      Redis::$value = 'value';
      Redis::$success = false;
  
      $driver = new RedisDriver('host', 6379);
  
      $this->assertNull($driver->getValue('index'));
    }

    public function testShouldSetValue()
    {  
      $driver = new RedisDriver('host', 6379);
      $driver->setValue('index', 'value');
  
      $this->assertSame('index', Redis::$index);
      $this->assertSame(serialize('value'), Redis::$value);
      $this->assertSame(0, Redis::$lifetime);
    }

    public function testShouldSetValueWithLifetime()
    {  
      $driver = new RedisDriver('host', 6379);
      $driver->setValue('index', 'value', 10);
  
      $this->assertSame('index', Redis::$index);
      $this->assertSame(serialize('value'), Redis::$value);
      $this->assertSame(10, Redis::$lifetime);
    }

    public function testShouldRemoveValue()
    {  
      $driver = new RedisDriver('host', 6379);
      $driver->removeValue('index');
  
      $this->assertSame('index', Redis::$index);
    }

    public function testShouldClear()
    {  
      $driver = new RedisDriver('host', 6379);
      $driver->clear();
  
      $this->assertTrue(Redis::$clear);
    }
  }
}