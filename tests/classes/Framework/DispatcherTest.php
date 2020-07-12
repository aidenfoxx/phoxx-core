<?php declare(strict_types=1);

namespace Phoxx\Core\Tests\Framework;

use Phoxx\Core\Controllers\Controller;
use Phoxx\Core\Framework\Dispatcher;
use Phoxx\Core\Framework\Exceptions\DispatchException;
use Phoxx\Core\Framework\ServiceContainer;
use Phoxx\Core\Http\Exceptions\RequestException;
use Phoxx\Core\Http\Exceptions\ResponseException;
use Phoxx\Core\Http\Helpers\SimpleRequest;
use Phoxx\Core\Http\RequestStack;
use Phoxx\Core\Http\Response;
use Phoxx\Core\Router\Route;
use Phoxx\Core\Router\RouteContainer;

use PHPUnit\Framework\TestCase;

final class MockController extends Controller
{
  public function success(): Response
  {
    return new Response('TEST_RESPONSE');
  }

  public function unexpected(): string
  {
    return 'UNEXPECTED_RESPONSE';
  }
}

final class DispatcherTest extends TestCase
{
  public function routeExceptionsProvider(): array
  {
    return [
      [['INVALID_CONTROLLER' => 'success'], DispatchException::class],
      [[MockController::class => 'invalid'], DispatchException::class],
      [[MockController::class => 'unexpected'], ResponseException::class],
    ];
  }

  public function testDispatch(): void
  {
    $routeContainer = new RouteContainer();
    $routeContainer->addRoute(new Route('/PATH', [MockController::class => 'success']));

    $dispatcher = new Dispatcher($routeContainer, new ServiceContainer(), new RequestStack());

    $this->assertInstanceOf(Response::class, $dispatcher->dispatch(new SimpleRequest('/PATH')));
  }

  public function testDispatchUndefinedRoute(): void
  {
    $dispatcher = new Dispatcher(new RouteContainer(), new ServiceContainer(), new RequestStack());

    $this->assertNull($dispatcher->dispatch(new SimpleRequest('/PATH')));
  }

  public function testDispatchExternalRequest(): void
  {
    $dispatcher = new Dispatcher(new RouteContainer(), new ServiceContainer(), new RequestStack());

    $this->expectException(RequestException::class);

    $dispatcher->dispatch(new SimpleRequest('http://www.test.com'));
  }

  /**
   * @dataProvider routeExceptionsProvider
   */
  public function testDispatchInvalidRoutes(array $action, string $exception): void
  {
    $routeContainer = new RouteContainer();
    $routeContainer->addRoute(new Route('/PATH', $action));

    $dispatcher = new Dispatcher($routeContainer, new ServiceContainer(), new RequestStack());

    $this->expectException($exception);

    $dispatcher->dispatch(new SimpleRequest('/PATH'));
  }
}


