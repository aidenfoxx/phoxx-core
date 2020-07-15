<?php

declare(strict_types=1);

namespace Phoxx\Core\Tests\Renderer;

use Phoxx\Core\Renderer\Interfaces\RendererDriver;
use Phoxx\Core\Renderer\Renderer;
use Phoxx\Core\Renderer\View;

use PHPUnit\Framework\TestCase;

final class RendererTest extends TestCase
{
  public function testGetDriver()
  {
    $mockDriver = $this->createMock(RendererDriver::class);
    $session = new Renderer($mockDriver);

    $this->assertSame($mockDriver, $session->getDriver());
  }

  public function testAddPath()
  {
    $mockDriver = $this->createMock(RendererDriver::class);
    $mockDriver->expects($this->once())
               ->method('addPath')
               ->with(
                 $this->equalTo('PATH'),
                 $this->equalTo('NAMESPACE')
               );

    $session = new Renderer($mockDriver);
    $session->addPath('PATH', 'NAMESPACE');
  }

  public function testRender()
  {
    $view = new View('PATH');
    $mockDriver = $this->createMock(RendererDriver::class);
    $mockDriver->expects($this->once())
               ->method('render')
               ->with($view)
               ->willReturn('OUTPUT');

    $session = new Renderer($mockDriver);

    $this->assertSame('OUTPUT', $session->render($view));
  }
}
