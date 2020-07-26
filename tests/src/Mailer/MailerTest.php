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
    $mailer = new Mailer($mockDriver);

    $this->assertSame($mockDriver, $mailer->getDriver());
  }

  public function testSend()
  {
    $mail = new Mail('SUBJECT', new View('PATH'));
    $mockDriver = $this->createMock(MailerDriver::class);
    $mockDriver->expects($this->at(0))
               ->method('send')
               ->with($mail);

    $mailer = new Mailer($mockDriver);
    $mailer->send($mail);
  }
}
