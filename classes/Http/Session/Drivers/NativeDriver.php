<?php

namespace Phoxx\Core\Http\Session\Drivers;

use Phoxx\Core\Http\Exceptions\ResponseException;
use Phoxx\Core\Http\Session\Interfaces\SessionDriver;
use Phoxx\Core\Http\Session\Exceptions\SessionException;

class NativeDriver implements SessionDriver
{
	public $active = false;

	public function __construct(string $sessionName)
	{
		session_register_shutdown();

		if ($sessionName !== null) {
			session_name($sessionName);
		}
	}

	public function getValue(string $index)
	{
		return $this->active === true && isset($_SESSION[$index]) === true ? $_SESSION[$index] : null;
	}

	public function flashValue(string $index)
	{
		if ($this->active === false || isset($_SESSION[$index]) === false) {
			return null;
		}

		$value = $_SESSION[$index];
		unset($_SESSION[$index]);
		
		return $value;
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

	public function open(): bool
	{
		if ($this->active === true) {
			return true;
		}

		if (session_status() === PHP_SESSION_ACTIVE) {
			throw new SessionException('Native session already active.');
		}

		if (headers_sent() === true) {
			throw new ResponseException('Response headers already sent.');
		}

	   	return session_start() === true ? ($this->active = true) : false;
	}

	public function close(): bool
	{
		return session_write_close();
	}

	public function clear(): void
	{
		session_unset();
	}
}