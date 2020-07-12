<?php

namespace Phoxx\Core\Utilities;

class Validator
{
  public const IS_TRUE = 'IS_TRUE';
  public const IS_FALSE = 'IS_FALSE';

  public const IS_EQUAL = 'IS_EQUAL';
  public const IS_EXACT = 'IS_EXACT';
  public const IS_MORE = 'IS_MORE';
  public const IS_LESS = 'IS_LESS';

  public const IS_NULL = 'IS_NULL';
  public const IS_ALPHA = 'IS_ALPHA';
  public const IS_ALPHANUMERIC = 'IS_ALPHANUMERIC';
  public const IS_ARRAY = 'IS_ARRAY';
  public const IS_BOOL = 'IS_BOOL';
  public const IS_DIGIT = 'IS_DIGIT';
  public const IS_DOUBLE = 'IS_DOUBLE';
  public const IS_FLOAT = 'IS_FLOAT';
  public const IS_INT = 'IS_INT';
  public const IS_LONG = 'IS_LONG';
  public const IS_LOWER = 'IS_LOWER';
  public const IS_EMPTY = 'IS_EMPTY';
  public const IS_NUMERIC = 'IS_NUMERIC';
  public const IS_OBJECT = 'IS_OBJECT';
  public const IS_MATCH = 'IS_MATCH';
  public const IS_SET = 'IS_SET';
  public const IS_STRING = 'IS_STRING';
  public const IS_UPPER = 'IS_UPPER';
  public const IS_IN_ARRAY = 'IS_IN_ARRAY';

  public const IS_EMAIL = 'IS_EMAIL';
  public const IS_PHONE = 'IS_PHONE';

  public const NOT_EQUAL = 'NOT_EQUAL';
  public const NOT_EXACT = 'NOT_EXACT';
  public const NOT_NULL = 'NOT_NULL';
  public const NOT_ALPHA = 'NOT_ALPHA';
  public const NOT_ALPHANUMERIC = 'NOT_ALPHANUMERIC';
  public const NOT_ARRAY = 'NOT_ARRAY';
  public const NOT_BOOL = 'NOT_BOOL';
  public const NOT_DIGIT = 'NOT_DIGIT';
  public const NOT_DOUBLE = 'NOT_DOUBLE';
  public const NOT_FLOAT = 'NOT_FLOAT';
  public const NOT_INT = 'NOT_INT';
  public const NOT_LONG = 'NOT_LONG';
  public const NOT_LOWER = 'NOT_LOWER';
  public const NOT_EMPTY = 'NOT_EMPTY';
  public const NOT_NUMERIC = 'NOT_NUMERIC';
  public const NOT_OBJECT = 'NOT_OBJECT';
  public const NOT_MATCH = 'NOT_MATCH';
  public const NOT_SET = 'NOT_SET';
  public const NOT_STRING = 'NOT_STRING';
  public const NOT_UPPER = 'NOT_UPPER';
  public const NOT_IN_ARRAY = 'NOT_IN_ARRAY';
  public const CUSTOM = 'CUSTOM';

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
