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
    $response = $controller->render(['key' => 'value'], Response::HTTP_OK, ['header' => 'value']);

    $this->assertSame('{"key":"value"}', $response->getContent());
    $this->assertSame(Response::HTTP_OK, $response->getStatus());
    $this->assertSame(['header' => 'value', 'Content-Type' => 'application/json'], $response->getHeaders());
  }
}
