<?php

namespace Phoxx\Core\Mailer\Interfaces;

use Phoxx\Core\Mailer\Mail;

interface MailerDriver
{
  public function send(Mail $mail): void;
}
