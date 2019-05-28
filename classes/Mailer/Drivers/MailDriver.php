<?php

namespace Phoxx\Core\Mailer\Drivers;

use Phoxx\Core\Mailer\Mail;
use Phoxx\Core\Mailer\Interfaces\MailerDriver;
use Phoxx\Core\Renderer\Renderer;

class MailDriver implements MailerDriver
{
	private $renderer;

	public function __construct(Renderer $renderer)
	{
		$this->renderer = $renderer;
	}

	public function send(Mail $mail): bool
	{
		$from = '';
		$to = array();
		$cc = array();
		$bcc = array();

		if (($email = $mail->getSender()) !== null) {
			if (($name = $mail->getSenderName()) !== null) {
				$from = $name.' <'.$email.'>';
			} else {
				$from = $email;
			}
		}

		foreach ($mail->getRecipients() as $email => $name) {
			$to[] = empty($name) === false ? $name.' <'.$email.'>' : $email;
		}

		foreach ($mail->getCc() as $email => $name) {
			$cc[] = empty($name) === false ? $name.' <'.$email.'>' : $email;
		}

		foreach ($mail->getBcc() as $email => $name) {
			$bcc[] = empty($name) === false ? $name.' <'.$email.'>' : $email;
		}

		$headers = array();

		$headers['MIME-Version'] = '1.0';
		$headers['Content-type'] = 'text/html;charset=UTF-8"';

		$headers['From'] = $from;
		$headers['Cc'] = implode(',', $cc);
		$headers['Bcc'] = implode(',', $bcc);

		return mail(
			implode(',', $to), 
			$mail->getSubject(), 
			$this->renderer->render($mail->getTemplate()), 
			$headers
		);
	}
}