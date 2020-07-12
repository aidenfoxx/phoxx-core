<?php

namespace Phoxx\Core\Session\Drivers;

use Phoxx\Core\Http\Exceptions\ResponseException;
use Phoxx\Core\Session\Exceptions\SessionException;
use Phoxx\Core\Session\Interfaces\SessionDriver;

class NativeDriver implements SessionDriver
{
  protected $sessionName;

  protected $sessionId;

	protected $active = false;

	public function __construct(?string $sessionName = null)
	{
		$this->sessionName = $sessionName !== null ? $sessionName : ini_get('session.name');

		session_register_shutdown();
	}

	public function getValue(string $index)
	{
		return $this->active === true && isset($_SESSION[$index]) === true ? $_SESSION[$index] : null;
	}

	public function setValue(string $index, $value): void
	{
		if ($this->active === true && isset($_SESSION) === true) {
			$_SESSION[$index] = $value;
		}
	}

	public function removeValue(string $index): void
	{
		if ($this->active === true && isset($_SESSION) === true) {
			unset($_SESSION[$index]);
		}
	}

	public function active(): bool
	{
		return $this->active;
	}

	public function open(): void
	{
		if ($this->active === true) {
			return;
		}

		if (headers_sent() === true) {
			throw new ResponseException('Response headers already sent.');
		}

		if (session_status() === PHP_SESSION_ACTIVE) {
			throw new SessionException('Native session already active.');
		}

    if (isset($_COOKIE[$this->sessionName]) === false) {
      $_COOKIE[$this->sessionName] = session_create_id();
    }

    session_name($this->sessionName);
    session_id($_COOKIE[$this->sessionName]);

		if (session_start() === false) {
			throw new SessionException('Failed to start session.');
		}

		$this->active = true;
	}

	public function close(): void
	{
		if ($this->active === true && session_write_close() === false) {
			throw new SessionException('Failed to close session.');
		}

		$this->active = false;
	}

  public function regenerate(): void
  {
    if ($this->active === true && session_regenerate_id() === true) {
      $_COOKIE[$this->sessionName] = session_name();
    }
  }

	public function clear(): void
	{
		session_unset();
	}
}
