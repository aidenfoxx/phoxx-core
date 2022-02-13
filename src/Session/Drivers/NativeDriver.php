<?php

namespace Phoxx\Core\Session\Drivers;

use Phoxx\Core\Exceptions\SessionException;
use Phoxx\Core\Exceptions\ResponseException;
use Phoxx\Core\Session\Session;

class NativeDriver implements Session
{
  protected $sessionName;

  protected $active = false;

  public function __construct(?string $sessionName = null)
  {
    $this->sessionName = $sessionName ? $sessionName : ini_get('session.name');

    session_register_shutdown();
  }

  public function getValue(string $index)
  {
    return $this->active && isset($_SESSION[$index]) ? $_SESSION[$index] : null;
  }

  public function setValue(string $index, $value): void
  {
    if ($this->active && isset($_SESSION)) {
      $_SESSION[$index] = $value;
    }
  }

  public function removeValue(string $index): void
  {
    if ($this->active && isset($_SESSION)) {
      unset($_SESSION[$index]);
    }
  }

  public function active(): bool
  {
    return $this->active;
  }

  public function open(): void
  {
    if ($this->active) {
      return;
    }

    if (headers_sent()) {
      throw new ResponseException('Response headers already sent.');
    }

    if (session_status() === PHP_SESSION_ACTIVE) {
      throw new SessionException('Native session already active.');
    }

    if (!isset($_COOKIE[$this->sessionName])) {
      $_COOKIE[$this->sessionName] = session_create_id();
    }

    session_name($this->sessionName);
    session_id($_COOKIE[$this->sessionName]);

    if (!session_start()) {
      throw new SessionException('Failed to start session.');
    }

    $this->active = true;
  }

  public function close(): void
  {
    if ($this->active && !session_write_close()) {
      throw new SessionException('Failed to close session.');
    }

    $this->active = false;
  }

  public function regenerate(): void
  {
    if ($this->active && session_regenerate_id()) {
      $_COOKIE[$this->sessionName] = session_id();
    }
  }

  public function clear(): void
  {
    session_unset();
  }
}
