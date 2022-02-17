<?php declare(strict_types=1);

namespace Phoxx\Core\Tests\Http;

use Phoxx\Core\Http\Route;
use Phoxx\Core\Exceptions\RouteException;

use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{
  public function testShouldCreateRotue()
  {
    $route = new Route('PATH', ['CONTROLLER' => 'ACTION'], 'METHOD');

    $this->assertSame('PATH', $route->getPattern());
    $this->assertSame(['CONTROLLER' => 'ACTION'], $route->getAction());
    $this->assertSame('METHOD', $route->getMethod());
  }

  public function testShouldReverseRoute(): void
  {
    $route = new Route('PATH/(?<PARAMETER>[A-Z]+)', ['CONTROLLER' => 'ACTION']);

    $this->assertSame('PATH/VALUE', $route->reverse(['PARAMETER' => 'VALUE']));
  }

  public function testShouldRejectMissingParameter(): void
  {
    $route = new Route('PATH/(?<PARAMETER>[A-Z]+)', ['CONTROLLER' => 'ACTION']);

    $this->expectException(RouteException::class);

    $route->reverse();
  }

  public function testShouldRejectInvalidParameter(): void
  {
    $route = new Route('PATH/(?<PARAMETER>[A-Z]+)', ['CONTROLLER' => 123]);

    $this->expectException(RouteException::class);

    $route->reverse();
  }
}
