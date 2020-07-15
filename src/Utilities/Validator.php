<?php

namespace Phoxx\Core\Utilities;

class Validator
{
  public const IS_TRUE = 'isTrue';

  public const IS_FALSE = 'isFalse';

  public const IS_EQUAL = 'isEqual';

  public const IS_EXACT = 'isExact';

  public const IS_MORE = 'isMore';

  public const IS_LESS = 'isLess';

  public const IS_NULL = 'isNull';

  public const IS_ALPHA = 'isAlpha';

  public const IS_ALPHANUMERIC = 'isAlphaNumeric';

  public const IS_ARRAY = 'isArray';

  public const IS_BOOL = 'isBool';

  public const IS_DIGIT = 'isDigit';

  public const IS_DOUBLE = 'isDouble';

  public const IS_FLOAT = 'isFloat';

  public const IS_INT = 'isInt';

  public const IS_LONG = 'isLong';

  public const IS_LOWER = 'isLower';

  public const IS_EMPTY = 'isEmpty';

  public const IS_NUMERIC = 'isNumeric';

  public const IS_OBJECT = 'isObject';

  public const IS_MATCH = 'isMatch';

  public const IS_SET = 'isSet';

  public const IS_STRING = 'isString';

  public const IS_UPPER = 'isUpper';

  public const IS_IN_ARRAY = 'isInArray';

  public const IS_EMAIL = 'isEmail';

  public const IS_PHONE = 'isPhone';

  public const NOT_EQUAL = 'notEqual';

  public const NOT_EXACT = 'notExact';

  public const NOT_NULL = 'notNull';

  public const NOT_ALPHA = 'notAlpha';

  public const NOT_ALPHANUMERIC = 'notAlphaNumeric';

  public const NOT_ARRAY = 'notArray';

  public const NOT_BOOL = 'notBool';

  public const NOT_DIGIT = 'notDigit';

  public const NOT_DOUBLE = 'notDouble';

  public const NOT_FLOAT = 'notFloat';

  public const NOT_INT = 'notInt';

  public const NOT_LONG = 'notLong';

  public const NOT_LOWER = 'notLower';

  public const NOT_EMPTY = 'notEmpty';

  public const NOT_NUMERIC = 'notNumeric';

  public const NOT_OBJECT = 'notObject';

  public const NOT_MATCH = 'notMatch';

  public const NOT_SET = 'notSet';

  public const NOT_STRING = 'notString';

  public const NOT_UPPER = 'notUpper';

  public const NOT_IN_ARRAY = 'notInArray';

  public const CUSTOM = 'CUSTOM';

  private static function isFalse($var1)
  {
    return $var1 === false;
  }

  private static function isTrue($var1)
  {
    return $var1 === true;
  }

  private static function isEqual($var1, $var2)
  {
    return $var1 == $var2;
  }

  private static function isExact($var1, $var2)
  {
    return $var1 === $var2;
  }

  private static function isMore($var1, $var2)
  {
    return $var1 > $var2;
  }

  private static function isLess($var1, $var2)
  {
    return $var1 < $var2;
  }

  private static function isNull($var1)
  {
    return is_null($var1);
  }

  private static function isAlpha($var1)
  {
    return ctype_alpha($var1);
  }

  private static function isAlphaNumeric($var1)
  {
    return ctype_alnum($var1);
  }

  private static function isArray($var1)
  {
    return is_array($var1);
  }

  private static function isBool($var1)
  {
    return is_bool($var1);
  }

  private static function isDigit($var1)
  {
    return ctype_digit($var1);
  }

  private static function isDouble($var1)
  {
    return is_double($var1);
  }

  private static function isFloat($var1)
  {
    return is_float($var1);
  }

  private static function isInt($var1)
  {
    return is_int($var1);
  }

  private static function isLong($var1)
  {
    return is_long($var1);
  }

  private static function isLower($var1)
  {
    return ctype_lower($var1);
  }

  private static function isEmpty($var1)
  {
    return empty($var1);
  }

  private static function isNumeric($var1)
  {
    return is_numeric($var1);
  }

  private static function isObject($var1)
  {
    return is_object($var1);
  }

  private static function isMatch($var1, $var2)
  {
    return (bool)preg_match($var1, $var2);
  }

  private static function isSet($var1)
  {
    return isset($var1);
  }

  private static function isString($var1)
  {
    return is_string($var1);
  }

  private static function isUpper($var1)
  {
    return ctype_upper($var1);
  }

  private static function isInArray($var1, $var2)
  {
    return in_array($var1, $var2);
  }

  private static function isEmail($var1)
  {
    return (bool)filter_var($var1, FILTER_VALIDATE_EMAIL);
  }

  private static function isPhone($var1)
  {
    return (bool)preg_match('#^[0-9]{8,15}$#', preg_replace('#[\s-]+#', '', $var1));
  }

  private static function notEqual($var1, $var2)
  {
    return ($var1 != $var2);
  }

  private static function notExact($var1, $var2)
  {
    return ($var1 !== $var2);
  }

  private static function notNull($var1)
  {
    return !is_null($var1);
  }

  private static function notAlpha($var1)
  {
    return !ctype_alpha($var1);
  }

  private static function notAlphaNumeric($var1)
  {
    return !ctype_alnum($var1);
  }

  private static function notArray($var1)
  {
    return !is_array($var1);
  }

  private static function notBool($var1)
  {
    return !is_bool($var1);
  }

  private static function notDigit($var1)
  {
    return !ctype_digit($var1);
  }

  private static function notDouble($var1)
  {
    return !is_double($var1);
  }

  private static function notFloat($var1)
  {
    return !is_float($var1);
  }

  private static function notInt($var1)
  {
    return !is_int($var1);
  }

  private static function notLong($var1)
  {
    return !is_long($var1);
  }

  private static function notLower($var1)
  {
    return !ctype_lower($var1);
  }

  private static function notEmpty($var1)
  {
    return !empty($var1);
  }

  private static function notNumeric($var1)
  {
    return !is_numeric($var1);
  }

  private static function notObject($var1)
  {
    return !is_object($var1);
  }

  private static function notMatch($var1, $var2)
  {
    return !(bool)preg_match($var1, $var2);
  }

  private static function notSet($var1)
  {
    return !isset($var1);
  }

  private static function notString($var1)
  {
    return !is_string($var1);
  }

  private static function notUpper($var1)
  {
    return !ctype_upper($var1);
  }

  private static function notInArray($var1, $var2)
  {
    return !in_array($var1, $var2);
  }

  private static function custom($callback)
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
