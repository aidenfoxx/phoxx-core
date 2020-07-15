<?php

namespace Phoxx\Core\Mailer;

use Phoxx\Core\Mailer\Interfaces\MailerDriver;
use Phoxx\Core\Framework\Interfaces\ServiceProvider;

class Mailer
{
  private $driver;

  public function __construct(MailerDriver $driver)
  {
    $this->driver = $driver;
  }

  public function getDriver(): MailerDriver
  {
    return $this->driver;
  }

  public function send(Mail $mail): bool
  {
    return $this->driver->send($mail);
  }
}
