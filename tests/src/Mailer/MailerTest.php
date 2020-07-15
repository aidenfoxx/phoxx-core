<?php

declare(strict_types=1);

namespace Phoxx\Core\Tests\Mailer;

use Phoxx\Core\Mailer\Interfaces\MailerDriver;
use Phoxx\Core\Mailer\Mail;
use Phoxx\Core\Mailer\Mailer;
use Phoxx\Core\Renderer\View;

use PHPUnit\Framework\TestCase;

final class MailerTest extends TestCase
{
  public function testGetDriver()
  {
    $mockDriver = $this->createMock(MailerDriver::class);
    $session = new Mailer($mockDriver);

    $this->assertSame($mockDriver, $session->getDriver());
  }

  public function testSend()
  {
    $mail = new Mail('SUBJECT', new View('PATH'));
    $mockDriver = $this->createMock(MailerDriver::class);
    $mockDriver->expects($this->at(0))
               ->method('send')
               ->with($mail)
               ->willReturn(true);
    $mockDriver->expects($this->at(1))
               ->method('send')
               ->with($mail)
               ->willReturn(false);

    $session = new Mailer($mockDriver);

    $this->assertSame(true, $session->send($mail));
    $this->assertSame(false, $session->send($mail));
  }
}
