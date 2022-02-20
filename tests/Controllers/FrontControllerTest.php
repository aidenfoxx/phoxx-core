<?php

declare(strict_types=1);

namespace Phoxx\Core\Tests\Controllers\Helpers;

use Phoxx\Core\Controllers\FrontController;
use Phoxx\Core\Exceptions\ServiceException;
use Phoxx\Core\Http\RequestStack;
use Phoxx\Core\Http\Response;
use Phoxx\Core\Http\Router;
use Phoxx\Core\Renderer\Renderer;
use Phoxx\Core\Renderer\View;
use Phoxx\Core\System\Services;

use PHPUnit\Framework\TestCase;

final class FrontControllerTest extends TestCase
{
  public function testShouldRender()
  {
    $view = new View('TEMPLATE');
    $renderer = $this->createMock(Renderer::class);
    $renderer->expects($this->once())->method('render')->with($view)->willReturn('CONTENT');

    $services = $this->createMock(Services::class);
    $services->expects($this->once())->method('getService')->with(Renderer::class)->willReturn($renderer);

    $controller = $this->getMockForAbstractClass(FrontController::class, [new Router($services), $services]);
    $response = $controller->render($view, Response::HTTP_OK, ['HEADER' => 'VALUE']);

    $this->assertSame('CONTENT', $response->getContent());
    $this->assertSame(Response::HTTP_OK, $response->getStatus());
    $this->assertSame(['HEADER' => 'VALUE'], $response->getHeaders());
  }

  public function testShouldRejectMissingService()
  {
    $services = new Services();
    $controller = $this->getMockForAbstractClass(FrontController::class, [new Router($services), $services]);

    $this->expectException(ServiceException::class);

    $controller->render(new View('TEMPLATE'));
  }
}
