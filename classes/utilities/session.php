<?php

namespace Phoxx\Core\Utilities;

class Session
{
	protected static $instance;

	public static function core(): self
	{
		if (isset(static::$instance) === false) {
			static::$instance = new static(Config::core()->getFile('core')->CORE_SESSION);
		}
		return static::$instance;
	}

	protected $name;

	public function __construct(string $name = null)
	{
		$this->name = $name !== null ? $name : php_ini('session.name');
	}

	/**
	 * Ensures valid session is always defined for
	 * method calls.
	 */
	public function __call(string $method, array $arguments)
	{
		if (method_exists($this, '_'.$method) === true) {
			if (session_name() !== $this->name) {
				if (session_status() === PHP_SESSION_ACTIVE) {
					session_write_close();
				}
				session_name($this->name);
				session_id(isset($_COOKIE[$this->name]) === true ? $_COOKIE[$this->name] : session_create_id());
				session_start();

				$_COOKIE[$this->name] = session_id();		
			}
			return call_user_func_array(array($this, '_'.$method), $arguments);
		}
		trigger_error('Call to undefined method '.__CLASS__.'::'.$method.'()', E_USER_ERROR);
	}

	public function _getValue(string $name)
	{
		return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
	}

	public function _setValue(string $name, $value): void
	{
		$_SESSION[$name] = $value;
	}

	public function _removeValue(string $name): void
	{
		unset($_SESSION[$name]);
	}

	public function _clear(): void
	{
		session_unset();
	}
}