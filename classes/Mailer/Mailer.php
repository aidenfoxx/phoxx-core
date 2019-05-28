<?php

namespace Phoxx\Core\Mailer;

use Phoxx\Core\Mailer\Interfaces\MailerDriver;
use Phoxx\Core\Framework\Interfaces\ServiceInterface;

class Mailer implements ServiceInterface
{
	private $driver;

	public function __construct(MailerDriver $driver)
	{
		$this->driver = $driver;
	}

	public function getServiceName(): string
	{
		return 'mailer';
	}

	public function send(Mail $mail): bool
	{
		return $this->driver->send($mail);
	}
}