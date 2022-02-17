<?php

namespace Phoxx\Core\Mailer;

use Phoxx\Core\File\File;
use Phoxx\Core\Renderer\View;

class Mail
{
  protected $subject;

  protected $template;

  protected $sender;

  protected $senderName;

  protected $headers;

  protected $recipients = [];

  protected $cc = [];

  protected $bcc = [];

  protected $attachments = [];

  public function __construct(
    string $subject,
    View $view,
    ?string $sender = null,
    ?string $senderName = null,
    array $headers = []
  ) {
    $this->subject = $subject;
    $this->view = $view;
    $this->sender = $sender;
    $this->senderName = $senderName;
    $this->headers = $headers;
  }

  public function getSubject(): string
  {
    return $this->subject;
  }

  public function getView(): View
  {
    return $this->view;
  }

  public function getSender(): ?string
  {
    return $this->sender;
  }

  public function getSenderName(): ?string
  {
    return $this->senderName;
  }

  public function getHeader(string $key): ?string
  {
    return $this->headers[$key] ?? null;
  }

  public function setHeader(string $key, string $value): void
  {
    $this->headers[$key] = $value;
  }

  public function getHeaders(): array
  {
    return $this->headers;
  }

  public function addRecipient(string $email, ?string $name = null): void
  {
    $this->recipients[$email] = $name;
  }

  public function getRecipients(): array
  {
    return $this->recipients;
  }

  public function addCC(string $email, ?string $name = null): void
  {
    $this->cc[$email] = $name;
  }

  public function getCC(): array
  {
    return $this->cc;
  }

  public function addBCC(string $email, ?string $name = null): void
  {
    $this->bcc[$email] = $name;
  }

  public function getBCC(): array
  {
    return $this->bcc;
  }

  public function addAttachment(File $file): void
  {
    $this->attachments[] = $file;
  }

  public function getAttachments(): array
  {
    return $this->attachments;
  }
}
