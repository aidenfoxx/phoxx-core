<?php

namespace Phoxx\Core\Utilities;

class Validator
{
  final public static function isFalse($var1)
  {
    return $var1 === false;
  }

  final public static function isTrue($var1)
  {
    return $var1 === true;
  }

  final public static function isEqual($var1, $var2)
  {
    return $var1 == $var2;
  }

  final public static function isExact($var1, $var2)
  {
    return $var1 === $var2;
  }

  final public static function isMore($var1, $var2)
  {
    return $var1 > $var2;
  }

  final public static function isLess($var1, $var2)
  {
    return $var1 < $var2;
  }

  final public static function isNull($var1)
  {
    return is_null($var1);
  }

  final public static function isAlpha($var1)
  {
    return ctype_alpha($var1);
  }

  final public static function isAlphaNumeric($var1)
  {
    return ctype_alnum($var1);
  }

  final public static function isArray($var1)
  {
    return is_array($var1);
  }

  final public static function isBool($var1)
  {
    return is_bool($var1);
  }

  final public static function isDigit($var1)
  {
    return ctype_digit($var1);
  }

  final public static function isDouble($var1)
  {
    return is_double($var1);
  }

  final public static function isFloat($var1)
  {
    return is_float($var1);
  }

  final public static function isInt($var1)
  {
    return is_int($var1);
  }

  final public static function isLong($var1)
  {
    return is_long($var1);
  }

  final public static function isLower($var1)
  {
    return ctype_lower($var1);
  }

  final public static function isEmpty($var1)
  {
    return empty($var1);
  }

  final public static function isNumeric($var1)
  {
    return is_numeric($var1);
  }

  final public static function isObject($var1)
  {
    return is_object($var1);
  }

  final public static function isMatch($var1, $var2)
  {
    return (bool)preg_match($var1, $var2);
  }

  final public static function isSet($var1)
  {
    return isset($var1);
  }

  final public static function isString($var1)
  {
    return is_string($var1);
  }

  final public static function isUpper($var1)
  {
    return ctype_upper($var1);
  }

  final public static function isInArray($var1, $var2)
  {
    return in_array($var1, $var2);
  }

  final public static function isEmail($var1)
  {
    return (bool)filter_var($var1, FILTER_VALIDATE_EMAIL);
  }

  final public static function isPhone($var1)
  {
    return (bool)preg_match('#^[0-9]{8,15}$#', preg_replace('#[\s-]+#', '', $var1));
  }

  final public static function notEqual($var1, $var2)
  {
    return ($var1 != $var2);
  }

  final public static function notExact($var1, $var2)
  {
    return ($var1 !== $var2);
  }

  final public static function notNull($var1)
  {
    return !is_null($var1);
  }

  final public static function notAlpha($var1)
  {
    return !ctype_alpha($var1);
  }

  final public static function notAlphaNumeric($var1)
  {
    return !ctype_alnum($var1);
  }

  final public static function notArray($var1)
  {
    return !is_array($var1);
  }

  final public static function notBool($var1)
  {
    return !is_bool($var1);
  }

  final public static function notDigit($var1)
  {
    return !ctype_digit($var1);
  }

  final public static function notDouble($var1)
  {
    return !is_double($var1);
  }

  final public static function notFloat($var1)
  {
    return !is_float($var1);
  }

  final public static function notInt($var1)
  {
    return !is_int($var1);
  }

  final public static function notLong($var1)
  {
    return !is_long($var1);
  }

  final public static function notLower($var1)
  {
    return !ctype_lower($var1);
  }

  final public static function notEmpty($var1)
  {
    return !empty($var1);
  }

  final public static function notNumeric($var1)
  {
    return !is_numeric($var1);
  }

  final public static function notObject($var1)
  {
    return !is_object($var1);
  }

  final public static function notMatch($var1, $var2)
  {
    return !(bool)preg_match($var1, $var2);
  }

  final public static function notSet($var1)
  {
    return !isset($var1);
  }

  final public static function notString($var1)
  {
    return !is_string($var1);
  }

  final public static function notUpper($var1)
  {
    return !ctype_upper($var1);
  }

  final public static function notInArray($var1, $var2)
  {
    return !in_array($var1, $var2);
  }

  final public static function custom($callback)
  {
    return call_user_func_array($callback, array_slice(func_get_args(), 1));
  }

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

      if (is_callable($function) === false || call_user_func_array($function, $rule) === false) {
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
