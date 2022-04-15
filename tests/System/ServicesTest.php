<?php declare(strict_types=1);

namespace Phoxx\Core\Tests\Framework;

use Phoxx\Core\System\Services;

use PHPUnit\Framework\TestCase;

use stdClass;

final class ServicesTest extends TestCase
{
  public function testShouldGetService(): void
  {
    $service = new stdClass();
    $services = new Services();
    $services->setService($service);

    $this->assertSame($service, $services->getService(stdClass::class));
  }

  public function testShouldGetServiceNull(): void
  {
    $services = new Services();

    $this->assertNull($services->getService('invalid'));
  }

  public function testShouldGetNamedService(): void
  {
    $service = new stdClass();
    $services = new Services();
    $services->setService($service, 'test');

    $this->assertSame($service, $services->getService('test'));
    $this->assertNull($services->getService(stdClass::class));
  }
}
