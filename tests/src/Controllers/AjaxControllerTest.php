<?php

declare(strict_types=1);

namespace Phoxx\Core\Tests\Controllers;

use Phoxx\Core\Controllers\AjaxController;
use Phoxx\Core\Framework\ServiceContainer;
use Phoxx\Core\Http\RequestStack;
use Phoxx\Core\Http\Response;
use Phoxx\Core\Router\RouteContainer;

use PHPUnit\Framework\TestCase;

final class AjaxControllerTest extends TestCase
{
  public function testRender()
  {
    $controller = $this->getMockForAbstractClass(AjaxController::class, [
      new RouteContainer(),
      new ServiceContainer(),
      new RequestStack()
    ]);
    $response = $controller->render(['RESPONSE' => 'VALUE'], Response::HTTP_CREATED, ['HEADER' => 'VALUE']);

    $this->assertSame('{"RESPONSE":"VALUE"}', $response->getContent());
    $this->assertSame(Response::HTTP_CREATED, $response->getStatus());
    $this->assertSame(['HEADER' => 'VALUE', 'Content-Type' => 'application/json'], $response->getHeaders());
  }
}
