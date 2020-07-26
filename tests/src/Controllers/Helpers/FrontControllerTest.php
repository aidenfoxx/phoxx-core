<?php

declare(strict_types=1);

namespace Phoxx\Core\Tests\Controllers\Helpers;

use Phoxx\Core\Controllers\Helpers\FrontController;
use Phoxx\Core\Framework\Exceptions\ServiceException;
use Phoxx\Core\Framework\ServiceContainer;
use Phoxx\Core\Http\RequestStack;
use Phoxx\Core\Http\Response;
use Phoxx\Core\Renderer\Renderer;
use Phoxx\Core\Renderer\View;
use Phoxx\Core\Router\RouteContainer;

use PHPUnit\Framework\TestCase;

final class FrontControllerTest extends TestCase
{
  public function testRender()
  {
    $view = new View('TEMPLATE');

    $mockRenderer = $this->createMock(Renderer::class);
    $mockRenderer->expects($this->once())
                 ->method('render')
                 ->with($view)
                 ->willReturn('RESPONSE');

    $mockServiceContainer = $this->createMock(ServiceContainer::class);
    $mockServiceContainer->expects($this->once())
                         ->method('getService')
                         ->with(Renderer::class)
                         ->willReturn($mockRenderer);

    $controller = $this->getMockForAbstractClass(FrontController::class, [
      new RouteContainer(),
      $mockServiceContainer,
      new RequestStack()
    ]);
    $response = $controller->render($view, Response::HTTP_CREATED, ['HEADER' => 'VALUE']);


    $this->assertSame('RESPONSE', $response->getContent());
    $this->assertSame(Response::HTTP_CREATED, $response->getStatus());
    $this->assertSame(['HEADER' => 'VALUE'], $response->getHeaders());
  }

  public function testRenderException()
  {
    $controller = $this->getMockForAbstractClass(FrontController::class, [
      new RouteContainer(),
      new ServiceContainer(),
      new RequestStack()
    ]);

    $this->expectException(ServiceException::class);

    $controller->render(new View('TEMPLATE'));
  }
}
