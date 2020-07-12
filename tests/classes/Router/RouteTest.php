<?php declare(strict_types=1);

namespace Phoxx\Core\Tests\Router;

use Phoxx\Core\Router\Route;
use Phoxx\Core\Router\Exceptions\RouteException;

use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{
  public function testGetValues()
  {
    $route = new Route('/PATH', ['CONTROLLER' => 'ACTION'], 'POST');

    $this->assertSame('/PATH', $route->getPattern());
    $this->assertSame(['CONTROLLER' => 'ACTION'], $route->getAction());
    $this->assertSame('POST', $route->getMethod());
  }

  public function testReverse(): void
  {
    $route = new Route('/PATH', ['CONTROLLER' => 'ACTION']);

    $this->assertSame('/PATH', $route->reverse());
  }

  public function testReverseWithParameters(): void
  {
    $route = new Route('/PATH/(?<PARAMETER>[A-Z]+)', ['CONTROLLER' => 'ACTION']);

    $this->assertSame('/PATH/VALUE', $route->reverse(['PARAMETER' => 'VALUE']));
  }

  public function testReverseException(): void
  {
    $route = new Route('/PATH/(?<PARAMETER>[A-Z]+)', ['CONTROLLER' => 'ACTION']);

    $this->expectException(RouteException::class);

    $route->reverse();
  }
}


