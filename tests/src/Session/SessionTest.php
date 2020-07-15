<?php

declare(strict_types=1);

namespace Phoxx\Core\Tests\Session;

use Phoxx\Core\Session\Session;
use Phoxx\Core\Session\Interfaces\SessionDriver;

use PHPUnit\Framework\TestCase;

final class SessionTest extends TestCase
{
  public function testGetDriver()
  {
    $mockDriver = $this->createMock(SessionDriver::class);
    $session = new Session($mockDriver);

    $this->assertSame($mockDriver, $session->getDriver());
  }

  public function testGetValue()
  {
    $mockDriver = $this->createMock(SessionDriver::class);
    $mockDriver->expects($this->once())
               ->method('getValue')
               ->with($this->equalTo('INDEX'))
               ->willReturn('VALUE');

    $session = new Session($mockDriver);

    $this->assertSame('VALUE', $session->getValue('INDEX'));
  }

  public function testSetValue()
  {
    $mockDriver = $this->createMock(SessionDriver::class);
    $mockDriver->expects($this->once())
               ->method('setValue')
               ->with(
                 $this->equalTo('INDEX'),
                 $this->equalTo('VALUE')
               );

    $session = new Session($mockDriver);
    $session->setValue('INDEX', 'VALUE');
  }

  public function testRemoveValue()
  {
    $mockDriver = $this->createMock(SessionDriver::class);
    $mockDriver->expects($this->once())
               ->method('removeValue')
               ->with($this->equalTo('INDEX'));

    $session = new Session($mockDriver);
    $session->removeValue('INDEX');
  }

  public function testActive()
  {
    $mockDriver = $this->createMock(SessionDriver::class);
    $mockDriver->expects($this->at(0))
               ->method('active')
               ->willReturn(false);
    $mockDriver->expects($this->at(1))
               ->method('active')
               ->willReturn(true);

    $session = new Session($mockDriver);

    $this->assertSame(false, $session->active());
    $this->assertSame(true, $session->active());
  }

  public function testOpen()
  {
    $mockDriver = $this->createMock(SessionDriver::class);
    $mockDriver->expects($this->once())
               ->method('open');

    $session = new Session($mockDriver);
    $session->open();
  }

  public function testClose()
  {
    $mockDriver = $this->createMock(SessionDriver::class);
    $mockDriver->expects($this->once())
               ->method('close');

    $session = new Session($mockDriver);
    $session->close();
  }

  public function testClear()
  {
    $mockDriver = $this->createMock(SessionDriver::class);
    $mockDriver->expects($this->once())
               ->method('clear');

    $session = new Session($mockDriver);
    $session->clear();
  }
}
