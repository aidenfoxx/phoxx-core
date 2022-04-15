<?php declare(strict_types=1);

namespace Phoxx\Core\Tests\Http;

use Phoxx\Core\Http\Route;
use Phoxx\Core\Exceptions\RouteException;

use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{
    public function testShouldCreateRotue()
    {
        $route = new Route('path', ['controller' => 'action'], 'method');

        $this->assertSame('path', $route->getPattern());
        $this->assertSame(['controller' => 'action'], $route->getAction());
        $this->assertSame('METHOD', $route->getMethod());
    }

    public function testShouldReverseRoute(): void
    {
        $route = new Route('path/(?<param>[a-z]+)', ['controller' => 'action']);

        $this->assertSame('path/value', $route->reverse(['param' => 'value']));
    }

    public function testShouldRejectMissingParameter(): void
    {
        $route = new Route('path/(?<param>[a-z]+)', ['controller' => 'action']);

        $this->expectException(RouteException::class);

        $route->reverse();
    }

    public function testShouldRejectInvalidParameter(): void
    {
        $route = new Route('path/(?<param>[a-z]+)', ['controller' => 'action']);

        $this->expectException(RouteException::class);

        $route->reverse(['param' => 123]);
    }
}
