<?php

declare(strict_types=1);

namespace Phoxx\Core\Tests\Renderer;

use Phoxx\Core\Renderer\View;
use Phoxx\Core\Renderer\Exceptions\ViewException;

use PHPUnit\Framework\TestCase;

final class ViewTest extends TestCase
{
  public function testGetTemplate()
  {
    $view = new View('PATH');

    $this->assertSame('PATH', $view->getTemplate());
  }

  public function testGetParameter()
  {
    $view = new View('PATH', ['PARAMETER' => 'VALUE']);

    $this->assertSame('VALUE', $view->getParameter('PARAMETER'));
  }

  public function testGetParameterNull()
  {
    $view = new View('PATH');

    $this->assertNull($view->getParameter('PARAMETER'));
  }

  public function testSetParameter()
  {
    $view = new View('PATH');
    $view->setParameter('PARAMETER', 'VALUE');

    $this->assertSame('VALUE', $view->getParameter('PARAMETER'));
  }

  public function testGetParameters(): void
  {
    $view = new View('PATH', ['PARAMETER' => 'VALUE']);

    $this->assertSame(['PARAMETER' => 'VALUE'], $view->getParameters());
  }
}
