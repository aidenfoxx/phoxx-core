<?php

namespace Phoxx\Core\Mailer\Drivers;

use Phoxx\Core\Mailer\Exceptions\MailException;
use Phoxx\Core\Mailer\Interfaces\MailerDriver;
use Phoxx\Core\Mailer\Mail;
use Phoxx\Core\Renderer\Renderer;

class MailDriver implements MailerDriver
{
  private $renderer;

  public function __construct(Renderer $renderer)
  {
    $this->renderer = $renderer;
  }

  /**
   * Send mail using native mail function.
   * @param  Mail $mail Mail content
   * @return void
   * @throws MailException If mail fails to send
   */
  public function send(Mail $mail): void
  {
    $from = null;
    $to = [];
    $cc = [];
    $bcc = [];

    if (($email = $mail->getSender()) !== null) {
      if (($name = $mail->getSenderName()) !== null) {
        $from = $name . ' <' . $email . '>';
      } else {
        $from = $email;
      }
    }

    foreach ($mail->getRecipients() as $email => $name) {
      $to[] = empty($name) === false ? $name . ' <' . $email . '>' : $email;
    }

    foreach ($mail->getCc() as $email => $name) {
      $cc[] = empty($name) === false ? $name . ' <' . $email . '>' : $email;
    }

    foreach ($mail->getBcc() as $email => $name) {
      $bcc[] = empty($name) === false ? $name . ' <' . $email . '>' : $email;
    }

    $headers = [];
    $headers['MIME-Version'] = '1.0';
    $headers['Content-type'] = 'text/html;charset=UTF-8"';
    $headers['From'] = $from;
    $headers['Cc'] = implode(',', $cc);
    $headers['Bcc'] = implode(',', $bcc);

    if (mail(implode(',', $to), $mail->getSubject(), $this->renderer->render($mail->getTemplate()), $headers) === false) {
      throw new MailException('Failed to send send mail.');
    }
  }
}
