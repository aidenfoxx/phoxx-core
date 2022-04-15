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
    $view = new View('path');
    $mail = new Mail('subject', $view, 'email', 'name', ['header' => 'value']);

    $this->assertSame('subject', $mail->getSubject());
    $this->assertSame($view, $mail->getView());
    $this->assertSame('email', $mail->getSender());
    $this->assertSame('name', $mail->getSenderName());
    $this->assertSame('value', $mail->getHeader('header'));
    $this->assertSame([
      'MIME-Version' => '1.0',
      'Content-Type' => 'text/html; charset=UTF-8',
      'header' => 'value'
    ], $mail->getHeaders());
  }

  public function testShouldSetHeader()
  {
    $mail = new Mail('subject', new View('path'));
    $mail->setHeader('header', 'value');

    $this->assertSame([
      'MIME-Version' => '1.0',
      'Content-Type' => 'text/html; charset=UTF-8',
      'header' => 'value'
    ], $mail->getHeaders());
  }

  public function testShouldGetHeaderNull()
  {
    $mail = new Mail('subject', new View('path'));

    $this->assertNull($mail->getHeader('invalid'));
  }

  public function testShouldAddRecipients()
  {
    $mail = new Mail('subject', new View('path'));
    $mail->addRecipient('email', 'name');

    $this->assertSame(['email' => 'name'], $mail->getRecipients());
  }

  public function testShouldAddCC()
  {
    $mail = new Mail('subject', new View('path'));
    $mail->addCC('email', 'name');

    $this->assertSame(['email' => 'name'], $mail->getCC());
  }

  public function testShouldAddBCC()
  {
    $mail = new Mail('subject', new View('path'));
    $mail->addBCC('email', 'name');

    $this->assertSame(['email' => 'name'], $mail->getBCC());
  }

  public function testShouldAddAttachments()
  {
    $file = new File(PATH_BASE . '/Mailer/MailTest/attachment.txt');
    $mail = new Mail('subject', new View('path'));
    $mail->addAttachment($file);

    $this->assertSame([$file], $mail->getAttachments());
  }
}
