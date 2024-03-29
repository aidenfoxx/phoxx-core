<?php declare(strict_types=1);

namespace Phoxx\Core\Tests\Http;

use Phoxx\Core\Controllers\Controller;
use Phoxx\Core\Exceptions\RequestException;
use Phoxx\Core\Exceptions\ResponseException;
use Phoxx\Core\Exceptions\RouteException;
use Phoxx\Core\Http\Response;
use Phoxx\Core\Http\Route;
use Phoxx\Core\Http\Router;
use Phoxx\Core\Http\Request;
use Phoxx\Core\System\Services;

use PHPUnit\Framework\TestCase;

final class TestController extends Controller
{
    static public $router;

    static public $services;

    static public $main;

    static public $active;

    public static function clear(): void
    {
        self::$router = null;
        self::$services = null;
        self::$main = null;
        self::$active = null;
    }

    public function __construct(Router $router, Services $services)
    {
        parent::__construct($router, $services);

        self::$router = $router;
        self::$services = $services;
    }

    public function action(): Response
    {
        return new Response();
    }

    public function invalid(): bool
    {
        return false;
    }

    public function subaction(): Response
    {
        if ($this->active() === $this->main()) {
            return $this->dispatch(new Request($this->active()->getPath()));
        }

        self::$main = $this->main();
        self::$active = $this->active();

        return new Response();
    }
}

final class InvalidController {
    
}

final class RouterTest extends TestCase
{
    public function invalidActions(): array
    {
        return [
            [['UndefinedController' => 'action']],
            [[InvalidController::class => 'action']],
            [[TestController::class => 'undefined']],
        ];
    }

    public function setUp(): void
    {
        TestController::clear();
    }

    public function testShouldCreateRouter(): void
    {
        $router = new Router(new Services());

        $this->assertNull($router->main());
        $this->assertNull($router->active());
    }

    public function testShouldDispatch(): void
    {
        $router = new Router(new Services());
        $router->addRoute(new Route('path', [TestController::class => 'action']));

        $this->assertInstanceOf(Response::class, $router->dispatch(new Request('path')));
        $this->assertInstanceOf(Router::class, TestController::$router);
        $this->assertInstanceOf(Services::class, TestController::$services);
    }

    public function testShouldDispatchNull(): void
    {
        $router = new Router(new Services());

        $this->assertNull($router->dispatch(new Request('path')));
    }

    public function testShouldRejectExternalRequest(): void
    {
        $router = new Router(new Services());

        $this->expectException(RequestException::class);

        $router->dispatch(new Request('http://www.test.com'));
    }

    public function testShouldRejectInvalidResponse(): void
    {
        $router = new Router(new Services());
        $router->addRoute(new Route('path', [TestController::class => 'invalid']));

        $this->expectException(ResponseException::class);

        $router->dispatch(new Request('path'));
    }

    /**
     * @dataProvider invalidActions
     */
    public function testShouldRejectInvalidAction(array $action): void
    {
        $router = new Router(new Services());
        $router->addRoute(new Route('path', $action));

        $this->expectException(RouteException::class);

        $router->dispatch(new Request('path'));
    }

    public function testShouldMatchRoute(): void
    {
        $router = new Router(new Services());
        $router->addRoute(new Route('(?<param1>[a-z]+)\/(?<param2>[0-9]+)\/([a-z]+)', [TestController::class => 'action']));
        $router->match('value/123/path', 'GET', $parameters);

        $this->assertSame(['param1' => 'value', 'param2' => '123'], $parameters);
    }

    public function testShouldMatchNullOnRouteNotFound(): void
    {
        $router = new Router(new Services());
        $router->addRoute(new Route('path', [TestController::class => 'action']));

        $this->assertNull($router->match('invalid'));
    }

    public function testShouldMatchNullOnMethodNotSet(): void
    {
        $router = new Router(new Services());

        $this->assertNull($router->match('invalid'));
    }

    public function testShouldReturnActiveRequest(): void
    {
        $router = new Router(new Services());
        $router->addRoute(new Route('path', [TestController::class => 'subaction']));

        $request = new Request('path');
        $router->dispatch($request);

        $this->assertSame($request, TestController::$main);
        $this->assertNotSame($request, TestController::$active);
    }
}
