<?php

declare(strict_types=1);

namespace Phoxx\Core\Tests\Cache;

use Phoxx\Core\Cache\Cache;
use Phoxx\Core\Cache\Interfaces\CacheDriver;

use PHPUnit\Framework\TestCase;

final class CacheTest extends TestCase
{
  public function testGetDriver()
  {
    $mockDriver = $this->createMock(CacheDriver::class);
    $cache = new Cache($mockDriver);

    $this->assertSame($mockDriver, $cache->getDriver());
  }

  public function testGetValue()
  {
    $mockDriver = $this->createMock(CacheDriver::class);
    $mockDriver->expects($this->once())
               ->method('getValue')
               ->with($this->equalTo('INDEX'))
               ->willReturn('VALUE');

    $cache = new Cache($mockDriver);

    $this->assertSame('VALUE', $cache->getValue('INDEX'));
  }

  public function testSetValue()
  {
    $mockDriver = $this->createMock(CacheDriver::class);
    $mockDriver->expects($this->once())
               ->method('setValue')
               ->with(
                 $this->equalTo('INDEX'),
                 $this->equalTo('VALUE'),
                 $this->equalTo(-1)
               );

    $cache = new Cache($mockDriver);
    $cache->setValue('INDEX', 'VALUE', -1);
  }

  public function testRemoveValue()
  {
    $mockDriver = $this->createMock(CacheDriver::class);
    $mockDriver->expects($this->once())
               ->method('removeValue')
               ->with($this->equalTo('INDEX'));

    $cache = new Cache($mockDriver);
    $cache->removeValue('INDEX');
  }

  public function testClear()
  {
    $mockDriver = $this->createMock(CacheDriver::class);
    $mockDriver->expects($this->once())
               ->method('clear');

    $cache = new Cache($mockDriver);
    $cache->clear();
  }
}
