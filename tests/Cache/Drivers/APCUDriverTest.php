<?php declare(strict_types=1);

namespace Phoxx\Core\Cache\Drivers
{
  final class ApcuDriverTestHelper
  {
    public static $index;

    public static $value;

    public static $lifetime;

    public static $success = true;

    public static $clear;

    public static function clear()
    {
      self::$index = null;
      self::$value = null;
      self::$lifetime = null;
      self::$success = true;
      self::$clear = null;
    }
  }

  function apcu_fetch($index, &$success)
  {
    ApcuDriverTestHelper::$index = $index;

    $success = ApcuDriverTestHelper::$success;
    
    return ApcuDriverTestHelper::$value;
  }

  function apcu_store($index, $value, $lifetime)
  {
    ApcuDriverTestHelper::$index = $index;
    ApcuDriverTestHelper::$value = $value;
    ApcuDriverTestHelper::$lifetime = $lifetime;
  }

  function apcu_delete($index)
  {
    ApcuDriverTestHelper::$index = $index;
  }

  function apcu_clear_cache()
  {
    ApcuDriverTestHelper::$clear = true;
  }
}

namespace Phoxx\Core\Tests\Cache\Drivers
{
  use Phoxx\Core\Cache\Drivers\ApcuDriver;
  use Phoxx\Core\Cache\Drivers\ApcuDriverTestHelper;

  use PHPUnit\Framework\TestCase;
  
  final class ApcuDriverTest extends TestCase
  {
    public function setUp(): void
    {
      ApcuDriverTestHelper::clear();
    }

    public function testShouldGetValue()
    {
      ApcuDriverTestHelper::$value = 'value';
  
      $driver = new ApcuDriver();
  
      $this->assertSame('value', $driver->getValue('index'));
      $this->assertSame('index', ApcuDriverTestHelper::$index);
    }

    public function testShouldGetValueNullOnSuccessFalse()
    {
      ApcuDriverTestHelper::$value = 'value';
      ApcuDriverTestHelper::$success = false;
  
      $driver = new ApcuDriver();
  
      $this->assertNull($driver->getValue('index'));
    }

    public function testShouldSetValue()
    {  
      $driver = new ApcuDriver();
      $driver->setValue('index', 'value');
  
      $this->assertSame('index', ApcuDriverTestHelper::$index);
      $this->assertSame('value', ApcuDriverTestHelper::$value);
      $this->assertSame(0, ApcuDriverTestHelper::$lifetime);
    }

    public function testShouldSetValueWithLifetime()
    {  
      $driver = new ApcuDriver();
      $driver->setValue('index', 'value', 10);
  
      $this->assertSame('index', ApcuDriverTestHelper::$index);
      $this->assertSame('value', ApcuDriverTestHelper::$value);
      $this->assertSame(10, ApcuDriverTestHelper::$lifetime);
    }

    public function testShouldRemoveValue()
    {  
      $driver = new ApcuDriver();
      $driver->removeValue('index');
  
      $this->assertSame('index', ApcuDriverTestHelper::$index);
    }

    public function testShouldClear()
    {  
      $driver = new ApcuDriver();
      $driver->clear();
  
      $this->assertTrue(ApcuDriverTestHelper::$clear);
    }
  }
}