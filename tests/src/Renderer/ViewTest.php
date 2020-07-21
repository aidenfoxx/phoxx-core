<?php

declare(strict_types=1);

namespace Phoxx\Core\Tests\Renderer;

use Phoxx\Core\Renderer\View;

use PHPUnit\Framework\TestCase;

final class ViewTest extends TestCase
{
  public function testView()
  {
    $view = new View('PATH', ['PARAMETER' => 'VALUE']);

    $this->assertSame('PATH', $view->getTemplate());
    $this->assertSame('VALUE', $view->getParameter('PARAMETER'));
    $this->assertSame(['PARAMETER' => 'VALUE'], $view->getParameters());
  }

  public function testGetParameter()
  {
    $view = new View('PATH');
    $view->setParameter('PARAMETER', 'VALUE');

    $this->assertSame('VALUE', $view->getParameter('PARAMETER'));
  }

  public function testGetParameterNull()
  {
    $view = new View('PATH');

    $this->assertNull($view->getParameter('PARAMETER'));
  }

  public function testGetParameters(): void
  {
    $view = new View('PATH');
    $view->setParameter('PARAMETER', 'VALUE');

    $this->assertSame(['PARAMETER' => 'VALUE'], $view->getParameters());
  }
}
