<?php

namespace Phoxx\Core\Utilities;

class Validator
{
	/**
	 * All consts should reference their
	 * respective static function in the
	 * same class.
	 */
	const IS_TRUE = 'IS_TRUE';
	const IS_FALSE = 'IS_FALSE';

	const IS_EQUAL = 'IS_EQUAL';
	const IS_EXACT = 'IS_EXACT';
	const IS_MORE = 'IS_MORE';
	const IS_LESS = 'IS_LESS';

	const IS_NULL = 'IS_NULL';
	const IS_ALPHA = 'IS_ALPHA';
	const IS_ALPHANUMERIC = 'IS_ALPHANUMERIC';
	const IS_ARRAY = 'IS_ARRAY';
	const IS_BOOL = 'IS_BOOL';
	const IS_DIGIT = 'IS_DIGIT';
	const IS_DOUBLE = 'IS_DOUBLE';
	const IS_FLOAT = 'IS_FLOAT';
	const IS_INT = 'IS_INT';
	const IS_LONG = 'IS_LONG';
	const IS_LOWER = 'IS_LOWER';
	const IS_EMPTY = 'IS_EMPTY';
	const IS_NUMERIC = 'IS_NUMERIC';
	const IS_OBJECT = 'IS_OBJECT';
	const IS_MATCH = 'IS_MATCH';
	const IS_SET = 'IS_SET';
	const IS_STRING = 'IS_STRING';
	const IS_UPPER = 'IS_UPPER';
	const IS_IN_ARRAY = 'IS_IN_ARRAY';

	const IS_EMAIL = 'IS_EMAIL';
	const IS_PHONE = 'IS_PHONE';

	const NOT_EQUAL = 'NOT_EQUAL';
	const NOT_EXACT = 'NOT_EXACT';

	const NOT_NULL = 'NOT_NULL';
	const NOT_ALPHA = 'NOT_ALPHA';
	const NOT_ALPHANUMERIC = 'NOT_ALPHANUMERIC';
	const NOT_ARRAY = 'NOT_ARRAY';
	const NOT_BOOL = 'NOT_BOOL';
	const NOT_DIGIT = 'NOT_DIGIT';
	const NOT_DOUBLE = 'NOT_DOUBLE';
	const NOT_FLOAT = 'NOT_FLOAT';
	const NOT_INT = 'NOT_INT';
	const NOT_LONG = 'NOT_LONG';
	const NOT_LOWER = 'NOT_LOWER';
	const NOT_EMPTY = 'NOT_EMPTY';
	const NOT_NUMERIC = 'NOT_NUMERIC';
	const NOT_OBJECT = 'NOT_OBJECT';
	const NOT_MATCH = 'NOT_MATCH';
	const NOT_SET = 'NOT_SET';
	const NOT_STRING = 'NOT_STRING';
	const NOT_UPPER = 'NOT_UPPER';
	const NOT_IN_ARRAY = 'NOT_IN_ARRAY';

	const CUSTOM = 'CUSTOM';

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
