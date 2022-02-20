<?php declare(strict_types=1);

namespace Phoxx\Core\Tests\Renderer;

use Phoxx\Core\Renderer\View;

use PHPUnit\Framework\TestCase;

final class ViewTest extends TestCase
{
  public function testShouldCreateView()
  {
    $view = new View('template', ['parameter' => 'value']);

    $this->assertSame('template', $view->getTemplate());
    $this->assertSame('value', $view->getParameter('parameter'));
    $this->assertSame(['parameter' => 'value'], $view->getParameters());
  }

  public function testShouldSetParameter()
  {
    $view = new View('template');
    $view->setParameter('parameter', 'value');

    $this->assertSame(['parameter' => 'value'], $view->getParameters());
  }

  public function testShouldGetParameterNull()
  {
    $view = new View('PATH');

    $this->assertNull($view->getParameter('invalid'));
  }
}
