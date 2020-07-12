<?php

namespace Phoxx\Core\Utilities;

class Validator
{
	private static function IS_FALSE($var1) { return $var1 === false; }
	private static function IS_TRUE($var1) { return $var1 === true; }

	private static function IS_EQUAL($var1, $var2) { return $var1 == $var2; }
	private static function IS_EXACT($var1, $var2) { return $var1 === $var2; }
	private static function IS_MORE($var1, $var2) { return $var1 > $var2; }
	private static function IS_LESS($var1, $var2) { return $var1 < $var2; }

	private static function IS_NULL($var1) { return is_null($var1); }
	private static function IS_ALPHA($var1) { return ctype_alpha($var1); }
	private static function IS_ALPHANUMERIC($var1) { return ctype_alnum($var1); }
	private static function IS_ARRAY($var1) { return is_array($var1); }
	private static function IS_BOOL($var1) { return is_bool($var1); }
	private static function IS_DIGIT($var1) { return ctype_digit($var1); }
	private static function IS_DOUBLE($var1) { return is_double($var1); }
	private static function IS_FLOAT($var1) { return is_float($var1); }
	private static function IS_INT($var1) { return is_int($var1); }
	private static function IS_LONG($var1) { return is_long($var1); }
	private static function IS_LOWER($var1) { return ctype_lower($var1); }
	private static function IS_EMPTY($var1) { return empty($var1); }
	private static function IS_NUMERIC($var1) { return is_numeric($var1); }
	private static function IS_OBJECT($var1) { return is_object($var1); }
	private static function IS_MATCH($var1, $var2) { return (bool)preg_match($var1, $var2); }
	private static function IS_SET($var1) { return isset($var1); }
	private static function IS_STRING($var1) { return is_string($var1); }
	private static function IS_UPPER($var1) { return ctype_upper($var1); }
	private static function IS_IN_ARRAY($var1, $var2) { return in_array($var1, $var2); }

	private static function IS_EMAIL($var1) { return (bool)filter_var($var1, FILTER_VALIDATE_EMAIL); }
	private static function IS_PHONE($var1) { return (bool)preg_match('#^[0-9]{8,15}$#', preg_replace('#[\s-]+#', '', $var1)); }

	private static function NOT_EQUAL($var1, $var2) { return ($var1 != $var2); }
	private static function NOT_EXACT($var1, $var2) { return ($var1 !== $var2); }

	private static function NOT_NULL($var1) { return !is_null($var1); }
	private static function NOT_ALPHA($var1) { return !ctype_alpha($var1); }
	private static function NOT_ALPHANUMERIC($var1) { return !ctype_alnum($var1); }
	private static function NOT_ARRAY($var1) { return !is_array($var1); }
	private static function NOT_BOOL($var1) { return !is_bool($var1); }
	private static function NOT_DIGIT($var1) { return !ctype_digit($var1); }
	private static function NOT_DOUBLE($var1) { return !is_double($var1); }
	private static function NOT_FLOAT($var1) { return !is_float($var1); }
	private static function NOT_INT($var1) { return !is_int($var1); }
	private static function NOT_LONG($var1) { return !is_long($var1); }
	private static function NOT_LOWER($var1) { return !ctype_lower($var1); }
	private static function NOT_EMPTY($var1) { return !empty($var1); }
	private static function NOT_NUMERIC($var1) { return !is_numeric($var1); }
	private static function NOT_OBJECT($var1) { return !is_object($var1); }
	private static function NOT_MATCH($var1, $var2) { return !(bool)preg_match($var1, $var2); }
	private static function NOT_SET($var1) { return !isset($var1); }
	private static function NOT_STRING($var1) { return !is_string($var1); }
	private static function NOT_UPPER($var1) { return !ctype_upper($var1); }
	private static function NOT_IN_ARRAY($var1, $var2) { return !in_array($var1, $var2); }

	private static function CUSTOM($callback) { return call_user_func_array($callback, array_slice(func_get_args(), 1)); }

	protected $errors = [];

	/**
	 * Returns any errors currently set.
	 * @return Array An array of error messages
	 */
	public function errors(): array
	{
		return $this->errors;
	}

	/**
	 * Perform validation(s) and store an
	 * error if any rule fails. Rule(s) format:
	 * [
	 *    [Validator::CALLBACK, ...$variables],
	 *    [Validator::CALLBACK, ...$variables],
	 *    ...
	 * ]
	 * @param  string $error The error message which will be stored
	 * @param  array  $rules An array of rules to validate
	 * @return boolean       Whether the validation passed or failed
	 */
	public function validate(array $rules, ?string $error = null): bool
	{
		foreach ($rules as $rule) {
			$function = array_shift($rule);

			if (is_callable([$this, $function]) === false || call_user_func_array([$this, $function], $rule) === false) {
				if ($error !== null) {
					$this->errors[] = $error;
				}
				return false;
			}
		}

		return true;
	}

	/**
	 * Clears any errors defined.
	 */
	public function clear(): void
	{
		$this->errors = [];
	}
}
