<?php declare(strict_types=1);

namespace Twig
{
    final class Environment
    {
        public static $loader;

        public static $config;

        public static $template;

        public static $parameters;

        public static function clear()
        {
            self::$loader = null;
            self::$config = null;
            self::$template = null;
            self::$parameters = null;
        }

        public function __construct($loader, $config)
        {
            self::$loader = $loader;
            self::$config = $config;
        }

        public function render($template, $parameters)
        {
            self::$template = $template;
            self::$parameters = $parameters;

            return 'value';
        }
    }
}

namespace Twig\Loader
{
    final class FilesystemLoader
    {
        const MAIN_NAMESPACE = 'main';

        public static $paths;

        public static $base;

        public static function clear()
        {
            self::$paths = null;
            self::$base = null;
        }

        public function __construct($paths, $base)
        {
            self::$paths = $paths;
            self::$base = $base;
        }

        public function addPath($path, $namespace = self::MAIN_NAMESPACE)
        {
            self::$paths[$namespace] = $path;
        }
    }
}

namespace Phoxx\Core\Tests\Renderer\Drivers
{
    use Twig\Environment;
    use Twig\Loader\FilesystemLoader;

    use Phoxx\Core\Renderer\Drivers\TwigDriver;
    use Phoxx\Core\Renderer\View;

    use PHPUnit\Framework\TestCase;
    
    final class TwigDriverTest extends TestCase
    {
        public function setUp(): void
        {
            Environment::clear();
            FilesystemLoader::clear();
        }

        public function testShouldCreateTwigDriver()
        {
            new TwigDriver();

            $this->assertSame([], FilesystemLoader::$paths);
            $this->assertSame(PATH_BASE, FilesystemLoader::$base);
            $this->assertInstanceOf(FilesystemLoader::class, Environment::$loader);
            $this->assertSame(['cache' => PATH_CACHE . '/twig'], Environment::$config);
        }

        public function testShouldCreateWithoutCache()
        {
            new TwigDriver(false);

            $this->assertSame(['cache' => false], Environment::$config);
        }

        public function testShouldGetTwig()
        {
            $driver = new TwigDriver(false);

            $this->assertInstanceOf(Environment::class, $driver->getTwig());
        }

        public function testShouldAddPath()
        {
            $driver = new TwigDriver();
            $driver->addPath('/path');

            $this->assertSame(['main' => '/path'], FilesystemLoader::$paths);
        }

        public function testShouldAddNamespace()
        {
            $driver = new TwigDriver();
            $driver->addPath('/path', 'namespace');

            $this->assertSame(['namespace' => '/path'], FilesystemLoader::$paths);
        }

        public function testShouldRender()
        {
            $view = new View('template', ['parameter' => 'value']);
            $driver = new TwigDriver();

            $this->assertSame('value', $driver->render($view));
            $this->assertSame('template.twig', Environment::$template);
            $this->assertSame(['parameter' => 'value'], Environment::$parameters);
        }
    }
}
