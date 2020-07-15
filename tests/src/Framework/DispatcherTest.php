<?php

declare(strict_types=1);

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

final class TestController extends Controller
{
  public function success(): Response
  {
    return new Response('RESPONSE');
  }

  public function unexpected(): string
  {
    return 'RESPONSE';
  }
}

// phpcs:ignore PSR1.Classes.ClassDeclaration
final class DispatcherTest extends TestCase
{
  public function routeExceptionsProvider(): array
  {
    return [
      [['INVALID_CONTROLLER' => 'success'], DispatchException::class],
      [[TestController::class => 'invalid'], DispatchException::class],
      [[TestController::class => 'unexpected'], ResponseException::class],
    ];
  }

  public function testDispatch(): void
  {
    $routeContainer = new RouteContainer();
    $routeContainer->addRoute(new Route('PATH', [TestController::class => 'success']));

    $dispatcher = $this->getMockForAbstractClass(Dispatcher::class, [
      $routeContainer,
      new ServiceContainer(),
      new RequestStack()
    ]);

    $this->assertInstanceOf(Response::class, $dispatcher->dispatch(new SimpleRequest('PATH')));
  }

  public function testDispatchUndefinedRoute(): void
  {
    $dispatcher = $this->getMockForAbstractClass(Dispatcher::class, [
      new RouteContainer(),
      new ServiceContainer(),
      new RequestStack()
    ]);

    $this->assertNull($dispatcher->dispatch(new SimpleRequest('PATH')));
  }

  public function testDispatchExternalRequest(): void
  {
    $dispatcher = $this->getMockForAbstractClass(Dispatcher::class, [
      new RouteContainer(),
      new ServiceContainer(),
      new RequestStack()
    ]);

    $this->expectException(RequestException::class);

    $dispatcher->dispatch(new SimpleRequest('http://www.test.com'));
  }

  /**
   * @dataProvider routeExceptionsProvider
   */
  public function testDispatchInvalidRoutes(array $action, string $exception): void
  {
    $routeContainer = new RouteContainer();
    $routeContainer->addRoute(new Route('PATH', $action));

    $dispatcher = $this->getMockForAbstractClass(Dispatcher::class, [
      $routeContainer,
      new ServiceContainer(),
      new RequestStack()
    ]);

    $this->expectException($exception);

    $dispatcher->dispatch(new SimpleRequest('PATH'));
  }
}
