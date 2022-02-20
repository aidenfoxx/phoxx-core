<?php declare(strict_types=1);

namespace
{
    final class Twig_Loader_Filesystem
    {
        public const MAIN_NAMESPACE = 'main';

        public static $paths;

        public static $base;

        public static $namespace;

        public function __construct(array $paths, string $base)
        {
            self::$paths = $paths;
            self::$base = $base;
        }

        public function addPath(string $path, ?string $namespace = null): void
        {
            self::$paths[] = $path;
            self::$namespace = $namespace;
        }
    }

    final class Twig_Environment
    {
        public static $loader;

        public static $config;

        public static $template;

        public static $parameters;

        public function __construct(Twig_Loader_Filesystem $loader, array $config)
        {
            self::$loader = $loader;
            self::$config = $config;
        }

        public function render(string $template, array $parameters): string
        {
            self::$template = $template;
            self::$parameters = $parameters;

            return 'Content';
        }
    }
}

namespace Phoxx\Core\Tests\Renderer\Drivers
{
    use Phoxx\Core\Renderer\Drivers\TwigDriver;
    use Phoxx\Core\Renderer\View;

    use Twig_Environment;
    use Twig_Loader_Filesystem;

    use PHPUnit\Framework\TestCase;
    
    final class TwigDriverTest extends TestCase
    {
        public function setUp(): void
        {
            Twig_Loader_Filesystem::$paths = null;
            Twig_Loader_Filesystem::$base = null;
            Twig_Loader_Filesystem::$namespace = null;
            
            Twig_Environment::$loader = null;
            Twig_Environment::$config = null;
            Twig_Environment::$template = null;
            Twig_Environment::$parameters = null;
        }

        public function testShouldCreateTwigDriver()
        {
            $driver = new TwigDriver();

            $this->assertSame([], Twig_Loader_Filesystem::$paths);
            $this->assertSame(PATH_BASE, Twig_Loader_Filesystem::$base);

            $this->assertInstanceOf(Twig_Loader_Filesystem::class, Twig_Environment::$loader);
            $this->assertSame(['cache' => PATH_CACHE . '/twig'], Twig_Environment::$config);

            $this->assertInstanceOf(Twig_Environment::class, $driver->getTwig());
        }

        public function testShouldCreateWithoutCache()
        {
            $driver = new TwigDriver(false);

            $this->assertSame(['cache' => false], Twig_Environment::$config);
        }

        public function testShouldAddPath()
        {
            $driver = new TwigDriver();
            $driver->addPath('/path');

            $this->assertSame(['/path'], Twig_Loader_Filesystem::$paths);
            $this->assertSame('main', Twig_Loader_Filesystem::$namespace);
        }

        public function testShouldAddNamespace()
        {
            $driver = new TwigDriver();
            $driver->addPath('/path', 'namespace');

            $this->assertSame(['/path'], Twig_Loader_Filesystem::$paths);
            $this->assertSame('namespace', Twig_Loader_Filesystem::$namespace);
        }

        public function testShouldRender()
        {
            $view = new View('template', ['parameter' => 'value']);
            $driver = new TwigDriver();
            $driver->render($view);

            $this->assertSame('template.twig', Twig_Environment::$template);
            $this->assertSame(['parameter' => 'value'], Twig_Environment::$parameters);
        }
    }
}
