<?php

declare(strict_types=1);

namespace Phoxx\Core\Tests\Router;

use Phoxx\Core\Router\RouteContainer;
use Phoxx\Core\Router\Route;

use PHPUnit\Framework\TestCase;

final class RouteContainerTest extends TestCase
{
  public function httpMethodsProvider(): array
  {
    return [
      ['GET'],
      ['POST'],
      ['PUT'],
      ['HEAD'],
      ['DELETE'],
      ['PATCH'],
      ['OPTIONS']
    ];
  }

  /**
   * @dataProvider httpMethodsProvider
   */
  public function testGetRoute($method): void
  {
    $route = new Route('PATH', ['CONTROLLER' => 'ACTION'], $method);
    $routeContainer = new RouteContainer();
    $routeContainer->addRoute($route);

    $this->assertSame($route, $routeContainer->getRoute('PATH', $method));
  }

  public function testGetRouteNull(): void
  {
    $routeContainer = new RouteContainer();

    $this->assertNull($routeContainer->getRoute('PATH'));
  }

  /**
   * @dataProvider httpMethodsProvider
   */
  public function testRemoveRoute($method): void
  {
    $routeContainer = new RouteContainer();
    $routeContainer->addRoute(new Route('PATH', ['CONTROLLER' => 'ACTION'], $method));
    $routeContainer->removeRoute('PATH', $method);

    $this->assertNull($routeContainer->getRoute('PATH'));
  }

  /**
   * @dataProvider httpMethodsProvider
   */
  public function testMatch($method): void
  {
    $route = new Route('PATH/(?<PARAMETER>[A-Z]+)', ['CONTROLLER' => 'ACTION'], $method);
    $routeContainer = new RouteContainer();
    $routeContainer->addRoute($route);

    $this->assertSame($route, $routeContainer->match('PATH/VALUE', $method, $parameters));
    $this->assertSame($parameters, ['PARAMETER' => 'VALUE']);
  }

  public function testMatchNull(): void
  {
    $routeContainer = new RouteContainer();

    $this->assertNull($routeContainer->match('PATH'));
  }
}
