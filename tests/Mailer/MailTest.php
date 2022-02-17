<?php declare(strict_types=1);

namespace Phoxx\Core\Tests\Mailer;

use Phoxx\Core\File\File;
use Phoxx\Core\Mailer\Mail;
use Phoxx\Core\Renderer\View;

use PHPUnit\Framework\TestCase;

final class MailTest extends TestCase
{
  public function testShouldCreateMail()
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

  public function testShouldGetHeader()
  {
    $mail = new Mail('SUBJECT', new View('PATH'));
    $mail->setHeader('HEADER', 'VALUE');

    $this->assertSame('VALUE', $mail->getHeader('HEADER'));
  }

  public function testShouldGetHeaderNull()
  {
    $mail = new Mail('SUBJECT', new View('PATH'));

    $this->assertNull($mail->getHeader('INVALID'));
  }

  public function testShouldGetHeaders()
  {
    $mail = new Mail('SUBJECT', new View('PATH'));
    $mail->setHeader('HEADER', 'VALUE');

    $this->assertSame(['HEADER' => 'VALUE'], $mail->getHeaders());
  }

  public function testShouldGetRecipients()
  {
    $mail = new Mail('SUBJECT', new View('PATH'));
    $mail->addRecipient('EMAIL', 'NAME');

    $this->assertSame(['EMAIL' => 'NAME'], $mail->getRecipients());
  }

  public function testShouldGetCC()
  {
    $mail = new Mail('SUBJECT', new View('PATH'));
    $mail->addCC('EMAIL', 'NAME');

    $this->assertSame(['EMAIL' => 'NAME'], $mail->getCC());
  }

  public function testShouldGetBCC()
  {
    $mail = new Mail('SUBJECT', new View('PATH'));
    $mail->addBCC('EMAIL', 'NAME');

    $this->assertSame(['EMAIL' => 'NAME'], $mail->getBCC());
  }

  public function testShouldGetAttachments()
  {
    $file = new File(PATH_BASE . '/Mailer/MailTest/attachment.txt');
    $mail = new Mail('SUBJECT', new View('PATH'));
    $mail->addAttachment($file);

    $this->assertSame([$file], $mail->getAttachments());
  }
}
