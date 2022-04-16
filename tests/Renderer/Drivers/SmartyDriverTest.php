<?php

namespace
{
    final class Smarty
    {
        const PHP_PASSTHRU = 'PHP_PASSTHRU';

        const PHP_REMOVE = 'PHP_REMOVE';

        public static $templateDir;
    
        public static $compileDir;
        
        public static $cacheDir;

        public static $security;

        public static $expectedTemplate;
    
        public static $parameters;
    
        public static $cleared;
    
        public static function clear()
        {
            self::$templateDir = null;
            self::$compileDir = null;
            self::$cacheDir = null;
            self::$security = null;
            self::$expectedTemplate = null;
            self::$parameters = null;
            self::$cleared = null;
        }
    
        public $caching;
    
        public $force_compile;
    
        public $escape_html;
    
        public function setTemplateDir($templateDir)
        {
            self::$templateDir = $templateDir;
        }
    
        public function setCompileDir($compileDir)
        {
            self::$compileDir = $compileDir;
        }
    
        public function setCacheDir($cacheDir)
        {
            self::$cacheDir = $cacheDir;
        }

        public function enableSecurity($security)
        {
            self::$security = $security;
        }
    
        public function templateExists($template)
        {
            return self::$expectedTemplate === $template;
        }

        public function assign($parameters)
        {
            self::$parameters = $parameters;
        }
    
        public function fetch($template)
        {            
            return 'content';
        }

        public function clearAllAssign()
        {
            self::$cleared = true;
        }
    }
}

namespace Phoxx\Core\Tests\Renderer\Drivers
{
    use Smarty;
    use Smarty_Security;

    use Phoxx\Core\Exceptions\FileException;
    use Phoxx\Core\Exceptions\RendererException;
    use Phoxx\Core\Renderer\Drivers\SmartyDriver;
    use Phoxx\Core\Renderer\View;
    
    use PHPUnit\Framework\TestCase;
    
    final class SmartyDriverTest extends TestCase
    {
        public function setUp(): void
        {
            Smarty::clear();
        }

        public function testShouldCreateSmartyDriver()
        {
            $driver = new SmartyDriver();
    
            $this->assertSame(PATH_BASE, Smarty::$templateDir);
            $this->assertSame(PATH_CACHE . '/smarty/templates_c', Smarty::$compileDir);
            $this->assertSame(PATH_CACHE . '/smarty/cache', Smarty::$cacheDir);
            $this->assertInstanceOf(Smarty_Security::class, Smarty::$security);
    
            $smarty = $driver->getSmarty();
    
            $this->assertSame(1, $smarty->caching);
            $this->assertFalse($smarty->force_compile);
            $this->assertTrue($smarty->escape_html);
        }
    
        public function testShouldCreateWithoutCache()
        {
            $driver = new SmartyDriver(false, true);
            $smarty = $driver->getSmarty();
    
            $this->assertSame(0, $smarty->caching);
            $this->assertTrue($smarty->force_compile);
        }

        public function testShouldRenderTemplate()
        {
            $view = new View('template', ['parameter' => 'value']);
            $driver = new SmartyDriver();
            $driver->addPath('./path');

            Smarty::$expectedTemplate = './path/template.tpl';
    
            $this->assertSame('content', $driver->render($view));
            $this->assertSame(['parameter' => 'value'], Smarty::$parameters);
            $this->assertTrue(Smarty::$cleared);
        }
    
        public function testShouldRenderNamespacedTemplate()
        {
            $view = new View('@namespace/template', ['parameter' => 'value']);
            $driver = new SmartyDriver();
            $driver->addPath('./path', 'namespace');
    
            Smarty::$expectedTemplate = './path/template.tpl';
    
            $this->assertSame('content', $driver->render($view));
        }

        public function testShouldRejectInvalidTemplate(): void
        {
            $view = new View('invalid');
            $driver = new SmartyDriver();
            $driver->addPath('./path');
    
            $this->expectException(FileException::class);
    
            $driver->render($view);
        }

        public function testShouldRejectInvalidNamespace(): void
        {
            $view = new View('@invalid/template');
            $driver = new SmartyDriver();
            $driver->addPath('./path');
    
            $this->expectException(RendererException::class);
    
            $driver->render($view);
        }
      
        public function testShouldRejectAbsoluteTemplate(): void
        {
            $view = new View(realpath(PATH_BASE));
            $driver = new SmartyDriver();
    
            $this->expectException(RendererException::class);
    
            $driver->render($view);
        }
    }
}
