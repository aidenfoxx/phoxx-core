<?php declare(strict_types=1);

namespace Phoxx\Core\Tests\Controllers\Helpers;

use Phoxx\Core\Controllers\AjaxController;
use Phoxx\Core\Http\RequestStack;
use Phoxx\Core\Http\Response;
use Phoxx\Core\Http\Router;
use Phoxx\Core\System\Services;

use PHPUnit\Framework\TestCase;

final class AjaxControllerTest extends TestCase
{
  public function testShouldRender()
  {
    $services = new Services();
    $controller = $this->getMockForAbstractClass(AjaxController::class, [new Router($services), $services]);
    $response = $controller->render(['KEY' => 'VALUE'], Response::HTTP_OK, ['HEADER' => 'VALUE']);

    $this->assertSame('{"KEY":"VALUE"}', $response->getContent());
    $this->assertSame(Response::HTTP_OK, $response->getStatus());
    $this->assertSame(['HEADER' => 'VALUE', 'Content-Type' => 'application/json'], $response->getHeaders());
  }
}
