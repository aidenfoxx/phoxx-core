<?php

declare(strict_types=1);

namespace Phoxx\Core\Http
{
  use Phoxx\Core\Tests\Http\DispatcherTest;

  function headers_sent(): bool
  {
    return DispatcherTest::$headersSent;
  }

  function header(string $header): void
  {
    DispatcherTest::$headers[] = $header;
  }
}

namespace Phoxx\Core\Tests\Http
{
  use Phoxx\Core\Controllers\Controller;
  use Phoxx\Core\Framework\ServiceContainer;
  use Phoxx\Core\Http\Dispatcher;
  use Phoxx\Core\Http\Exceptions\RequestException;
  use Phoxx\Core\Http\Exceptions\ResponseException;
  use Phoxx\Core\Http\Helpers\SimpleRequest;
  use Phoxx\Core\Http\RequestStack;
  use Phoxx\Core\Http\Response;
  use Phoxx\Core\Router\Exceptions\RouteException;
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
    public static $headers = [];

    public static $headersSent = false;

    public function responseStatusProvider(): array
    {
      return [[200], [404], [500]];
    }

    public function routeExceptionsProvider(): array
    {
      return [
        [['INVALID_CONTROLLER' => 'success'], RouteException::class],
        [[TestController::class => 'invalid'], RouteException::class],
        [[TestController::class => 'unexpected'], ResponseException::class],
      ];
    }

    public function setUp(): void
    {
      self::$headers = [];
      self::$headersSent = false;
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

    /**
     * @dataProvider responseStatusProvider
     */
    public function testSend(int $status): void
    {
      $dispatcher = new Dispatcher(
        new RouteContainer(),
        new ServiceContainer(),
        new RequestStack()
      );

      $this->expectOutputString('RESPONSE');

      $dispatcher->send(new Response('RESPONSE', $status));

      $this->assertSame($status, http_response_code());
    }

    public function testSendHeaders(): void
    {
      $dispatcher = new Dispatcher(
        new RouteContainer(),
        new ServiceContainer(),
        new RequestStack()
      );
      $dispatcher->send(new Response('RESPONSE', 200, [
        'HEADER_1' => 'VALUE_1',
        'HEADER_2' => 'VALUE_2'
      ]));

      $this->assertSame(200, http_response_code());
      $this->assertSame(self::$headers, [
        'HEADER_1: VALUE_1',
        'HEADER_2: VALUE_2'
      ]);
    }

    public function testSendAfterHeadersSent(): void
    {
      self::$headersSent = true;

      $dispatcher = new Dispatcher(
        new RouteContainer(),
        new ServiceContainer(),
        new RequestStack()
      );

      $this->expectException(ResponseException::class);

      $dispatcher->send(new Response('RESPONSE'));
    }
  }
}
