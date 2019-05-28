<?php

namespace Phoxx\Core\Mailer\Drivers;

use Phoxx\Core\Mailer\Mail;
use Phoxx\Core\Mailer\Interfaces\MailerDriver;

class SmtpDriver implements MailerDriver
{
	/**
	 * TODO: Implement.
	 */
	public function send(Mail $mail): bool
	{
		return true;
	}
}