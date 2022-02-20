<?php

namespace Phoxx\Core\Mailer\Drivers;

use Phoxx\Core\Exceptions\MailException;
use Phoxx\Core\Mailer\Mail;
use Phoxx\Core\Mailer\Mailer;
use Phoxx\Core\Renderer\Renderer;

class MailDriver implements Mailer
{
    private $renderer;

    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Send mail using native mail function.
     * @param    Mail $mail Mail content
     * @return void
     * @throws MailException If mail fails to send
     */
    public function send(Mail $mail): void
    {
        if (empty($mail->getRecipients())) {
            throw new MailException('No recipient(s) defined.');
        }

        $headers = $mail->getHeaders();

        if ($email = $mail->getSender()) {
            $headers['From'] = ($name = $mail->getSenderName()) ? $name . ' <' . $email . '>' : $email;
        }

        foreach ($mail->getRecipients() as $email => $name) {
            $headers['To'] = (isset($headers['To']) ? $headers['To'] . ', ' : '') . ($name ? $name . ' <' . $email . '>' : $email);
        }

        foreach ($mail->getCc() as $email => $name) {
            $headers['CC'] = (isset($headers['CC']) ? $headers['CC'] . ', ' : '') . ($name ? $name . ' <' . $email . '>' : $email);
        }

        foreach ($mail->getBcc() as $email => $name) {
            $headers['BCC'] = (isset($headers['BCC']) ? $headers['BCC'] . ', ' : '') . ($name ? $name . ' <' . $email . '>' : $email);
        }

        $success = mail(
            $headers['To'],
            $mail->getSubject(),
            $this->renderer->render($mail->getView()),
            array_change_key_case($headers)
        );

        if (!$success) {
            throw new MailException('Failed to send send mail.');
        }
    }
}
