<?php

declare(strict_types=1);

namespace Phoxx\Core\Tests\Utilities;

use Phoxx\Core\Utilities\Validator;

use PHPUnit\Framework\TestCase;

use stdClass;

final class ValidatorTest extends TestCase
{
  public function validationCasesProvider(): array
  {
    $SET = 'VALUE';
    $UNSET = null;

    $IS_TRUE = [true];
    $IS_FALSE = [false];
    $IS_EQUAL = [true, 'true'];
    $IS_EXACT = [true, true];
    $IS_MORE = [1, 0];
    $IS_LESS = [0, 1];
    $IS_NULL = [null];
    $IS_ALPHA = ['VALUE'];
    $IS_ALPHANUMERIC = ['VALUE123'];
    $IS_ARRAY = [['VALUE']];
    $IS_BOOL = [true];
    $IS_DIGIT = ['123'];
    $IS_DOUBLE = [123.45];
    $IS_FLOAT = [123.45];
    $IS_INT = [123];
    $IS_LONG = [123];
    $IS_LOWER = ['value'];
    $IS_EMPTY = [[]];
    $IS_NUMERIC = [123];
    $IS_OBJECT = [new stdClass()];
    $IS_MATCH = ['/VALUE/', 'VALUE'];
    $IS_SET = [$SET];
    $IS_STRING = ['VALUE'];
    $IS_UPPER = ['VALUE'];
    $IS_IN_ARRAY = ['VALUE', ['VALUE']];
    $IS_EMAIL = ['email@test.com'];
    $IS_PHONE = ['0760101010'];

    $NOT_EQUAL = [true, false];
    $NOT_EXACT = [true, 'true'];
    $NOT_NULL = [false];
    $NOT_ALPHA = ['123'];
    $NOT_ALPHANUMERIC = ['_VALUE'];
    $NOT_ARRAY = ['VALUE'];
    $NOT_BOOL = ['VALUE'];
    $NOT_DIGIT = ['VALUE'];
    $NOT_DOUBLE = ['VALUE'];
    $NOT_FLOAT = ['VALUE'];
    $NOT_INT = ['VALUE'];
    $NOT_LONG = ['VALUE'];
    $NOT_LOWER = ['VALUE'];
    $NOT_EMPTY = [['VALUE']];
    $NOT_NUMERIC = ['VALUE'];
    $NOT_OBJECT = [['VALUE']];
    $NOT_MATCH = ['/VALUE/', 'INVALID'];
    $NOT_SET = [$UNSET];
    $NOT_STRING = [123];
    $NOT_UPPER = ['value'];
    $NOT_IN_ARRAY = ['VALUE', ['INVALID']];

    $CUSTOM = function ($result) {
      return $result;
    };

    return [
      [Validator::IS_TRUE, $IS_TRUE, $IS_FALSE],
      [Validator::IS_FALSE, $IS_FALSE, $IS_TRUE],
      [Validator::IS_EQUAL, $IS_EQUAL, $NOT_EQUAL],
      [Validator::IS_EXACT, $IS_EXACT, $NOT_EXACT],
      [Validator::IS_MORE, $IS_MORE, [0, 0]],
      [Validator::IS_LESS, $IS_LESS, [0, 0]],
      [Validator::IS_NULL, $IS_NULL, $NOT_NULL],
      [Validator::IS_ALPHA, $IS_ALPHA, $NOT_ALPHA],
      [Validator::IS_ALPHANUMERIC, $IS_ALPHANUMERIC, $NOT_ALPHANUMERIC],
      [Validator::IS_ARRAY, $IS_ARRAY, $NOT_ARRAY],
      [Validator::IS_BOOL, $IS_BOOL, $NOT_BOOL],
      [Validator::IS_DIGIT, $IS_DIGIT, $NOT_DIGIT],
      [Validator::IS_DOUBLE, $IS_DOUBLE, $NOT_DOUBLE],
      [Validator::IS_FLOAT, $IS_FLOAT, $NOT_FLOAT],
      [Validator::IS_INT, $IS_INT, $NOT_INT],
      [Validator::IS_LONG, $IS_LONG, $NOT_LONG],
      [Validator::IS_LOWER, $IS_LOWER, $NOT_LOWER],
      [Validator::IS_EMPTY, $IS_EMPTY, $NOT_EMPTY],
      [Validator::IS_NUMERIC, $IS_NUMERIC, $NOT_NUMERIC],
      [Validator::IS_OBJECT, $IS_OBJECT, $NOT_OBJECT],
      [Validator::IS_MATCH, $IS_MATCH, $NOT_MATCH],
      [Validator::IS_SET, $IS_SET, $NOT_SET],
      [Validator::IS_STRING, $IS_STRING, $NOT_STRING],
      [Validator::IS_UPPER, $IS_UPPER, $NOT_UPPER],
      [Validator::IS_IN_ARRAY, $IS_IN_ARRAY, $NOT_IN_ARRAY],
      [Validator::IS_EMAIL, $IS_EMAIL, ['INVALID_EMAIL']],
      [Validator::IS_PHONE, $IS_PHONE, ['INVALID_PHONE']],
      [Validator::NOT_EQUAL, $NOT_EQUAL, $IS_EQUAL],
      [Validator::NOT_EXACT, $NOT_EXACT, $IS_EXACT],
      [Validator::NOT_NULL, $NOT_NULL, $IS_NULL],
      [Validator::NOT_ALPHA, $NOT_ALPHA, $IS_ALPHA],
      [Validator::NOT_ALPHANUMERIC, $NOT_ALPHANUMERIC, $IS_ALPHANUMERIC],
      [Validator::NOT_ARRAY, $NOT_ARRAY, $IS_ARRAY],
      [Validator::NOT_BOOL, $NOT_BOOL, $IS_BOOL],
      [Validator::NOT_DIGIT, $NOT_DIGIT, $IS_DIGIT],
      [Validator::NOT_DOUBLE, $NOT_DOUBLE, $IS_DOUBLE],
      [Validator::NOT_FLOAT, $NOT_FLOAT, $IS_FLOAT],
      [Validator::NOT_INT, $NOT_INT, $IS_INT],
      [Validator::NOT_LONG, $NOT_LONG, $IS_LONG],
      [Validator::NOT_LOWER, $NOT_LOWER, $IS_LOWER],
      [Validator::NOT_EMPTY, $NOT_EMPTY, $IS_EMPTY],
      [Validator::NOT_NUMERIC, $NOT_NUMERIC, $IS_NUMERIC],
      [Validator::NOT_OBJECT, $NOT_OBJECT, $IS_OBJECT],
      [Validator::NOT_MATCH, $NOT_MATCH, $IS_MATCH],
      [Validator::NOT_SET, $NOT_SET, $IS_SET],
      [Validator::NOT_STRING, $NOT_STRING, $IS_STRING],
      [Validator::NOT_UPPER, $NOT_UPPER, $IS_UPPER],
      [Validator::NOT_IN_ARRAY, $NOT_IN_ARRAY, $IS_IN_ARRAY],
      [Validator::CUSTOM, [$CUSTOM, true], [$CUSTOM, false]]
    ];
  }

  /**
   * @dataProvider validationCasesProvider
   */
  public function testValidate(string $method, array $success, array $error): void
  {
    $validator = new Validator();

    $this->assertTrue($validator->validate([array_merge([$method], $success)], 'ERROR'));
    $this->assertCount(0, $validator->getErrors());

    $this->assertFalse($validator->validate([array_merge([$method], $error)], 'ERROR'));
    $this->assertSame(['ERROR'], $validator->getErrors());
  }

  public function testMultipleValidate()
  {
    $validator = new Validator();

    $this->assertTrue($validator->validate([
      [Validator::IS_MORE, 10, 5],
      [Validator::IS_LESS, 10, 15],
    ], 'ERROR'));
    $this->assertCount(0, $validator->getErrors());

    $this->assertFalse($validator->validate([
      [Validator::IS_MORE, 20, 5],
      [Validator::IS_LESS, 20, 15],
    ], 'ERROR'));
    $this->assertSame(['ERROR'], $validator->getErrors());
  }

  public function testClearErrors()
  {
    $validator = new Validator();

    $this->assertFalse($validator->validate([[Validator::IS_TRUE, false]], 'ERROR'));
    $this->assertSame(['ERROR'], $validator->getErrors());

    $validator->clear();

    $this->assertCount(0, $validator->getErrors());
  }
}
