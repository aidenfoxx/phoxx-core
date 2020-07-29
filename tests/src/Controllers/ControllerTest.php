<?php

declare(strict_types=1);

namespace Phoxx\Core\Tests\Controllers;

use Phoxx\Core\Controllers\Controller;
use Phoxx\Core\Framework\ServiceContainer;
use Phoxx\Core\Http\Helpers\SimpleRequest;
use Phoxx\Core\Http\RequestStack;
use Phoxx\Core\Router\RouteContainer;

use PHPUnit\Framework\TestCase;

use stdClass;

final class ControllerTest extends TestCase
{
  public function testGetService()
  {
    $service = new stdClass();
    $serviceContainer = new ServiceContainer();
    $serviceContainer->setService($service);

    $controller = $this->getMockForAbstractClass(Controller::class, [
      new RouteContainer(),
      $serviceContainer,
      new RequestStack()
    ]);

    $this->assertSame($service, $controller->getService(stdClass::class));
  }

  public function testGetServiceNull()
  {
    $controller = $this->getMockForAbstractClass(Controller::class, [
      new RouteContainer(),
      new ServiceContainer(),
      new RequestStack()
    ]);

    $this->assertNull($controller->getService('SERVICE'));
  }

  public function testMain()
  {
    $request = new SimpleRequest('MAIN');
    $requestStack = new RequestStack();
    $requestStack->push($request);

    $controller = $this->getMockForAbstractClass(Controller::class, [
      new RouteContainer(),
      new ServiceContainer(),
      $requestStack
    ]);

    $this->assertSame($request, $controller->main());
  }

  public function testActive()
  {
    $requestMain = new SimpleRequest('MAIN');
    $requestActive = new SimpleRequest('ACTIVE');
    $requestStack = new RequestStack();
    $requestStack->push($requestMain);
    $requestStack->push($requestActive);

    $controller = $this->getMockForAbstractClass(Controller::class, [
      new RouteContainer(),
      new ServiceContainer(),
      $requestStack
    ]);

    $this->assertSame($requestMain, $controller->main());
    $this->assertSame($requestActive, $controller->active());
  }
}
