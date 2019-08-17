<?php

namespace Phoxx\Core\Mailer;

use Phoxx\Core\Renderer\View;

class Mail
{
	protected $subject;

	protected $template;

	protected $sender;

	protected $senderName;

	protected $headers;

	protected $recipients = array();

	protected $cc = array();

	protected $bcc = array();

	protected $attachments = array();

	public function __construct(string $subject, View $template, ?string $sender = null, ?string $senderName = null, array $headers = array())
	{
		$this->subject = $subject;
		$this->template = $template;
		$this->sender = $sender;
		$this->senderName = $senderName;
		$this->headers = array();
	}

	public function getSubject(): string
	{
		return $this->subject;
	}

	public function getTemplate(): View
	{
		return $this->template;
	}

	public function getSender(): ?string
	{
		return $this->sender;
	}

	public function getSenderName(): ?string
	{
		return $this->senderName;
	}

	public function getHeader(string $key): string
	{
		return $this->headers[$key];
	}

	public function setHeader(string $key, string $value): void
	{
		$this->headers[$key] = $value;
	}

	public function getHeaders(): array
	{
		return $this->headers;
	}

	public function getRecipients(): array
	{
		return $this->recipients;
	}

	public function addRecipient(string $email, ?string $name = null): void
	{
		$this->recipients[$email] = $name;
	}

	public function getCc(): array
	{
		return $this->cc;
	}

	public function addCc(string $email, ?string $name = null): void
	{
		$this->cc[$email] = $name;
	}

	public function getBcc(): array
	{
		return $this->bcc;
	}

	public function addBcc(string $email, ?string $name = null): void
	{
		$this->bcc[$email] = $name;
	}

	public function getAttachments(): array
	{
		return $this->attachments;
	}

	public function addAttachment($file): void
	{
		$this->attachments[] = $file;
	}
}