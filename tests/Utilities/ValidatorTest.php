<?php declare(strict_types=1);

namespace Phoxx\Core\Tests\Utilities;

use Phoxx\Core\Utilities\Validator;

use PHPUnit\Framework\TestCase;

use stdClass;

final class ValidatorTest extends TestCase
{
  public function successRules(): array
  {
    $isSet = 'TEST';
    $notSet = null;

    // Validation rules that should pass
    return [
      [Validator::IS_TRUE, true],
      [Validator::IS_FALSE, false],
      [Validator::IS_EQUAL, true, 'true'],
      [Validator::IS_EXACT, true, true],
      [Validator::IS_MORE, 1, 0],
      [Validator::IS_LESS, 0, 1],
      [Validator::IS_NULL, null],
      [Validator::IS_ALPHA, 'TEST'],
      [Validator::IS_ALPHANUMERIC, 'TEST123'],
      [Validator::IS_ARRAY, []],
      [Validator::IS_BOOL, true],
      [Validator::IS_DIGIT, '123'],
      [Validator::IS_DOUBLE, 123.45],
      [Validator::IS_FLOAT, 123.45],
      [Validator::IS_INT, 123],
      [Validator::IS_LONG, 123],
      [Validator::IS_LOWER, 'test'],
      [Validator::IS_EMPTY, []],
      [Validator::IS_NUMERIC, 123],
      [Validator::IS_OBJECT, new stdClass()],
      [Validator::IS_MATCH, '/TEST/', 'TEST'],
      [Validator::IS_SET, $isSet],
      [Validator::IS_STRING, 'TEST'],
      [Validator::IS_UPPER, 'TEST'],
      [Validator::IS_IN_ARRAY, 'TEST', ['TEST']],
      [Validator::IS_EMAIL, 'email@test.com'],
      [Validator::IS_PHONE, '0700000000'],
      [Validator::NOT_EQUAL, true, false],
      [Validator::NOT_EXACT, true, 'true'],
      [Validator::NOT_NULL, false],
      [Validator::NOT_ALPHA, '123'],
      [Validator::NOT_ALPHANUMERIC, '_VALUE'],
      [Validator::NOT_ARRAY, 'TEST'],
      [Validator::NOT_BOOL, 'TEST'],
      [Validator::NOT_DIGIT, 'TEST'],
      [Validator::NOT_DOUBLE, 'TEST'],
      [Validator::NOT_FLOAT, 'TEST'],
      [Validator::NOT_INT, 'TEST'],
      [Validator::NOT_LONG, 'TEST'],
      [Validator::NOT_LOWER, 'TEST'],
      [Validator::NOT_EMPTY, ['TEST']],
      [Validator::NOT_NUMERIC, 'TEST'],
      [Validator::NOT_OBJECT, ['TEST']],
      [Validator::NOT_MATCH, '/TEST/', 'VALUE'],
      [Validator::NOT_SET, $notSet],
      [Validator::NOT_STRING, 123],
      [Validator::NOT_UPPER, 'test'],
      [Validator::NOT_IN_ARRAY, 'TEST', ['VALUE']],
      [Validator::CUSTOM, function() { return true; }]
    ];
  }

  public function errorRules(): array
  {
    $isSet = 'TEST';
    $notSet = null;

    // Validation rules that should fail
    return [
      [Validator::IS_TRUE, false],
      [Validator::IS_FALSE, true],
      [Validator::IS_EQUAL, true, false],
      [Validator::IS_EXACT, true, 'true'],
      [Validator::IS_MORE, 0, 0],
      [Validator::IS_LESS, 0, 0],
      [Validator::IS_NULL, false],
      [Validator::IS_ALPHA, '123'],
      [Validator::IS_ALPHANUMERIC, '_TEST'],
      [Validator::IS_ARRAY, 'TEST'],
      [Validator::IS_BOOL, 'TEST'],
      [Validator::IS_DIGIT, 'TEST'],
      [Validator::IS_DOUBLE, 'TEST'],
      [Validator::IS_FLOAT, 'TEST'],
      [Validator::IS_INT, 'TEST'],
      [Validator::IS_LONG, 'TEST'],
      [Validator::IS_LOWER, 'TEST'],
      [Validator::IS_EMPTY, ['TEST']],
      [Validator::IS_NUMERIC, 'TEST'],
      [Validator::IS_OBJECT, ['TEST']],
      [Validator::IS_MATCH, '/TEST/', 'VALUE'],
      [Validator::IS_SET, $notSet],
      [Validator::IS_STRING, 123],
      [Validator::IS_UPPER, 'test'],
      [Validator::IS_IN_ARRAY, 'TEST', ['VALUE']],
      [Validator::IS_EMAIL, 'TEST'],
      [Validator::IS_PHONE, 'TEST'],
      [Validator::NOT_EQUAL, true, 'true'],
      [Validator::NOT_EXACT, true, true],
      [Validator::NOT_NULL, null],
      [Validator::NOT_ALPHA, 'TEST'],
      [Validator::NOT_ALPHANUMERIC, 'TEST123'],
      [Validator::NOT_ARRAY, []],
      [Validator::NOT_BOOL, true],
      [Validator::NOT_DIGIT, '123'],
      [Validator::NOT_DOUBLE, 123.45],
      [Validator::NOT_FLOAT, 123.45],
      [Validator::NOT_INT, 123],
      [Validator::NOT_LONG, 123],
      [Validator::NOT_LOWER, 'test'],
      [Validator::NOT_EMPTY, []],
      [Validator::NOT_NUMERIC, 123],
      [Validator::NOT_OBJECT, new stdClass()],
      [Validator::NOT_MATCH, '/TEST/', 'TEST'],
      [Validator::NOT_SET, $isSet],
      [Validator::NOT_STRING, 'TEST'],
      [Validator::NOT_UPPER, 'TEST'],
      [Validator::NOT_IN_ARRAY, 'TEST', ['TEST']],
      [Validator::CUSTOM, function () { return false; }]
    ];
  }

  /**
   * @dataProvider successRules
   */
  public function testShouldValidateSuccess(): void
  {
    $validator = new Validator();

    $this->assertTrue($validator->validate([func_get_args()], 'ERROR'));
    $this->assertSame([], $validator->getErrors());
  }

  /**
   * @dataProvider errorRules
   */
  public function testShouldValidateError(): void
  {
    $validator = new Validator();

    $this->assertFalse($validator->validate([func_get_args()], 'ERROR'));
    $this->assertSame(['ERROR'], $validator->getErrors());
  }

  public function testShouldHandleMultipleRules()
  {
    $validator = new Validator();

    $this->assertTrue($validator->validate([[Validator::IS_TRUE, true], [Validator::IS_FALSE, false]], 'error'));
    $this->assertCount(0, $validator->getErrors());

    $this->assertFalse($validator->validate([[Validator::IS_TRUE, false], [Validator::IS_FALSE, true]], 'error'));
    $this->assertSame(['error'], $validator->getErrors());
  }

  public function testShouldReturnMultipleErrors()
  {
    $validator = new Validator();

    $this->assertFalse($validator->validate([[Validator::IS_TRUE, false]], 'error 1'));
    $this->assertFalse($validator->validate([[Validator::IS_FALSE, true]], 'error 2'));
    $this->assertSame(['error 1', 'error 2'], $validator->getErrors());
  }

  public function testShouldClearErrors()
  {
    $validator = new Validator();

    $this->assertFalse($validator->validate([[Validator::IS_TRUE, false]], 'error'));

    $validator->clear();

    $this->assertSame([], $validator->getErrors());
  }
}
