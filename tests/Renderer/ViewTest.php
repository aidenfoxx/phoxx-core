<?php declare(strict_types=1);

namespace Phoxx\Core\Tests\Renderer;

use Phoxx\Core\Renderer\View;

use PHPUnit\Framework\TestCase;

final class ViewTest extends TestCase
{
  public function testShouldCreateView()
  {
    $view = new View('PATH', ['KEY' => 'VALUE']);

    $this->assertSame('PATH', $view->getTemplate());
    $this->assertSame('VALUE', $view->getParameter('KEY'));
    $this->assertSame(['KEY' => 'VALUE'], $view->getParameters());
  }

  public function testShouldGetParameter()
  {
    $view = new View('PATH');
    $view->setParameter('KEY', 'VALUE');

    $this->assertSame('VALUE', $view->getParameter('KEY'));
  }

  public function testShouldGetParameterNull()
  {
    $view = new View('PATH');

    $this->assertNull($view->getParameter('INVALID'));
  }


  public function testGetParameters(): void
  {
    $view = new View('PATH');
    $view->setParameter('KEY', 'VALUE');

    $this->assertSame(['KEY' => 'VALUE'], $view->getParameters());
  }
}
