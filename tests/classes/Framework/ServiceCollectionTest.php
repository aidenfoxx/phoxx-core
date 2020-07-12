<?php declare(strict_types=1);

namespace Phoxx\Core\Tests\Dispatcher;

use Phoxx\Core\Framework\ServiceContainer;

use PHPUnit\Framework\TestCase;

class MockService
{

}

final class ServiceContainerTest extends TestCase
{
  public function testGetService(): void
  {
    $service = new MockService();
    $serviceContainer = new ServiceContainer();

    $serviceContainer->addService($service);
    $this->assertSame($service, $serviceContainer->getService(MockService::class));
  }

  public function testGetServiceNull(): void
  {
    $serviceContainer = new ServiceContainer();

    $this->assertNull($serviceContainer->getService('SERVICE'));
  }

  public function testRemoveService(): void
  {
    $service = new MockService();
    $serviceContainer = new ServiceContainer();

    $serviceContainer->addService($service);
    $serviceContainer->removeService(MockService::class);
    $this->assertNull($serviceContainer->getService(MockService::class));
  }
}
