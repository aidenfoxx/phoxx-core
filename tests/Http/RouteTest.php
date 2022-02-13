<?php declare(strict_types=1);

namespace Phoxx\Core\Tests\Router;

use Phoxx\Core\Router\Route;
use Phoxx\Core\Router\Exceptions\RouteException;

use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{
  public function testRotue()
  {
    $route = new Route('PATH', ['CONTROLLER' => 'ACTION'], 'POST');

    $this->assertSame('PATH', $route->getPattern());
    $this->assertSame(['CONTROLLER' => 'ACTION'], $route->getAction());
    $this->assertSame('POST', $route->getMethod());
  }

  public function testGetPath(): void
  {
    $route = new Route('PATH', ['CONTROLLER' => 'ACTION']);

    $this->assertSame('PATH', $route->getPath());
  }

  public function testGetPathWithParameters(): void
  {
    $route = new Route('PATH/(?<PARAMETER>[A-Z]+)', ['CONTROLLER' => 'ACTION']);

    $this->assertSame('PATH/VALUE', $route->getPath(['PARAMETER' => 'VALUE']));
  }

  public function testGetPathException(): void
  {
    $route = new Route('PATH/(?<PARAMETER>[A-Z]+)', ['CONTROLLER' => 'ACTION']);

    $this->expectException(RouteException::class);

    $route->getPath();
  }
}
