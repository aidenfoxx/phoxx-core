<?php

declare(strict_types=1);

namespace Phoxx\Core\Tests\Framework;

use Phoxx\Core\Framework\ServiceContainer;

use PHPUnit\Framework\TestCase;

use stdClass;

final class ServiceContainerTest extends TestCase
{
  public function testGetService(): void
  {
    $service = new stdClass();
    $serviceContainer = new ServiceContainer();
    $serviceContainer->addService($service);

    $this->assertSame($service, $serviceContainer->getService(stdClass::class));
  }

  public function testGetServiceNull(): void
  {
    $serviceContainer = new ServiceContainer();

    $this->assertNull($serviceContainer->getService('SERVICE'));
  }

  public function testRemoveService(): void
  {
    $service = new stdClass();
    $serviceContainer = new ServiceContainer();
    $serviceContainer->addService($service);
    $serviceContainer->removeService(stdClass::class);

    $this->assertNull($serviceContainer->getService(stdClass::class));
  }
}
