<?php declare(strict_types=1);

namespace Phoxx\Core\Tests\Controllers;

use Phoxx\Core\Controllers\Controller;
use Phoxx\Core\Http\Request;
use Phoxx\Core\Http\Router;
use Phoxx\Core\System\Services;

use PHPUnit\Framework\TestCase;

use stdClass;

final class ControllerTest extends TestCase
{
  public function testShouldGetService()
  {
    $service = new stdClass();
    $services = new Services();
    $services->setService($service);
    $controller = $this->getMockForAbstractClass(Controller::class, [new Router($services), $services]);

    $this->assertSame($service, $controller->getService(stdClass::class));
  }

  public function testShouldGetServiceNull()
  {
    $services = new Services();
    $controller = $this->getMockForAbstractClass(Controller::class, [new Router($services), $services]);

    $this->assertNull($controller->getService('invalid'));
  }

  public function testShouldGetMainRequest()
  {
    $request = new Request('uri');
    $router = $this->createMock(Router::class);
    $router->expects($this->once())->method('main')->willReturn($request);
    $controller = $this->getMockForAbstractClass(Controller::class, [$router, new Services()]);

    $this->assertSame($request, $controller->main());
  }

  public function testShouldGetActiveRequest()
  {
    $request = new Request('uri');
    $router = $this->createMock(Router::class);
    $router->expects($this->once())->method('active')->willReturn($request);
    $controller = $this->getMockForAbstractClass(Controller::class, [$router, new Services()]);

    $this->assertSame($request, $controller->active());
  }
}
