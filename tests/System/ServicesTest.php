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

    $this->assertNull($services->getService('INVALID'));
  }


  public function testShouldGetNamedService(): void
  {
    $service = new stdClass();
    $services = new Services();
    $services->setService($service, 'TEST');

    $this->assertSame($service, $services->getService('TEST'));
    $this->assertNull($services->getService(stdClass::class));
  }

  public function testShouldRemoveService(): void
  {
    $service = new stdClass();
    $services = new Services();
    $services->setService($service);
    $services->removeService(stdClass::class);

    $this->assertNull($services->getService(stdClass::class));
  }
}
