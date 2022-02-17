<?php declare(strict_types=1);

namespace Phoxx\Core\Database\Model;

use Phoxx\Core\Database\Model;
use Phoxx\Core\Utilities\Validator;

use PHPUnit\Framework\TestCase;

final class ModelTest extends TestCase
{
  public function testShouldGetValidator()
  {
    $model = $this->getMockForAbstractClass(Model::class);
  
    $this->assertInstanceOf(Validator::class, $model->getValidator());
  }

  public function testShouldGetDateCreated()
  {
    $now = time();
    $model = $this->getMockForAbstractClass(Model::class);
    $model->setDateCreated($now);

    $this->assertSame($now, $model->getDateCreated());
  }

  public function testShouldGetDateUpdated()
  {
    $now = time();
    $model = $this->getMockForAbstractClass(Model::class);
    $model->setDateUpdated($now);

    $this->assertSame($now, $model->getDateUpdated());
  }
}
