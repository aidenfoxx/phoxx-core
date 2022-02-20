<?php

namespace Phoxx\Core\Tests\Renderer\Drivers;

use Phoxx\Core\Exceptions\FileException;
use Phoxx\Core\Exceptions\RendererException;
use Phoxx\Core\Renderer\Drivers\PhpDriver;
use Phoxx\Core\Renderer\View;

use PHPUnit\Framework\TestCase;

final class PhpDriverTest extends TestCase
{
    public function testShouldRenderTemplate()
    {
        $view = new View('template', ['parameter' => 'value']);
        $driver = new PhpDriver();
        $driver->addPath('./Renderer/Drivers/PhpDriverTest');

        $this->assertSame('value', $driver->render($view));
    }

    public function testShouldRenderNamespacedTemplate()
    {
        $view = new View('@namespace/template', ['parameter' => 'value']);
        $driver = new PhpDriver();
        $driver->addPath('./Renderer/Drivers/PhpDriverTest', 'namespace');

        $this->assertSame('value', $driver->render($view));
    }

    public function testShouldRenderAbsolutePath(): void
    {
        $view = new View('template', ['parameter' => 'value']);
        $driver = new PhpDriver();
        $driver->addPath(realpath(PATH_BASE) . '/Renderer/Drivers/PhpDriverTest');

        $this->assertSame('value', $driver->render($view));
    }

    public function testShouldRejectInvalidTemplate(): void
    {
        $view = new View('invalid');
        $driver = new PhpDriver();
        $driver->addPath('./Renderer/Drivers/PhpDriverTest');

        $this->expectException(FileException::class);

        $driver->render($view);
    }
  
    public function testShouldRejectInvalidNamespace(): void
    {
        $view = new View('@invalid/template');
        $driver = new PhpDriver();
        $driver->addPath('./Renderer/Drivers/PhpDriverTest');

        $this->expectException(RendererException::class);

        $driver->render($view);
    }
  
    public function testShouldRejectAbsoluteTemplate(): void
    {
        $view = new View(realpath(PATH_BASE) . '/System/ConfigTest/config');
        $driver = new PhpDriver();
        $driver->addPath('./Renderer/Drivers/PhpDriverTest');

        $this->expectException(RendererException::class);

        $driver->render($view);
    }
}
