<?php

declare(strict_types=1);

namespace Phoxx\Core\Tests\Mailer;

use Phoxx\Core\Mailer\Mail;
use Phoxx\Core\Renderer\View;

use PHPUnit\Framework\TestCase;

final class MailTest extends TestCase
{
  public function testView()
  {
    $view = new View('PATH');
    $mail = new Mail('SUBJECT', $view, 'SENDER', 'SENDER_NAME', ['HEADER' => 'VALUE']);

    $this->assertSame('SUBJECT', $mail->getSubject());
    $this->assertSame($view, $mail->getView());
    $this->assertSame('SENDER', $mail->getSender());
    $this->assertSame('SENDER_NAME', $mail->getSenderName());
    $this->assertSame('VALUE', $mail->getHeader('HEADER'));
    $this->assertSame(['HEADER' => 'VALUE'], $mail->getHeaders());
  }

  public function testGetHeader()
  {
    $mail = new Mail('SUBJECT', new View('PATH'));
    $mail->setHeader('HEADER', 'VALUE');

    $this->assertSame('VALUE', $mail->getHeader('HEADER'));
  }

  public function testGetHeaderNull()
  {
    $mail = new Mail('SUBJECT', new View('PATH'));

    $this->assertNull($mail->getHeader('HEADER'));
  }

  public function testGetHeaders()
  {
    $mail = new Mail('SUBJECT', new View('PATH'));
    $mail->setHeader('HEADER', 'VALUE');

    $this->assertSame(['HEADER' => 'VALUE'], $mail->getHeaders());
  }

  public function testGetRecipients()
  {
    $mail = new Mail('SUBJECT', new View('PATH'));
    $mail->addRecipient('EMAIL', 'NAME');

    $this->assertSame(['EMAIL' => 'NAME'], $mail->getRecipients());
  }

  public function testGetCc()
  {
    $mail = new Mail('SUBJECT', new View('PATH'));
    $mail->addCc('EMAIL', 'NAME');

    $this->assertSame(['EMAIL' => 'NAME'], $mail->getCc());
  }

  public function testGetBcc()
  {
    $mail = new Mail('SUBJECT', new View('PATH'));
    $mail->addBcc('EMAIL', 'NAME');

    $this->assertSame(['EMAIL' => 'NAME'], $mail->getBcc());
  }

  public function testGetAttachments()
  {
    $mail = new Mail('SUBJECT', new View('PATH'));
    $mail->addAttachment('FILE');

    $this->assertSame(['FILE'], $mail->getAttachments());
  }
}
