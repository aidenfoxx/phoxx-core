<?php declare(strict_types=1);

namespace Phoxx\Core\Framework
{
  use Phoxx\Core\Tests\Framework\ApplicationTest;

  function headers_sent(): bool
  {
    return ApplicationTest::$headersSent;
  }

  function header(string $header): void
  {
    ApplicationTest::$headers[] = $header;
  }
}

namespace Phoxx\Core\Tests\Framework
{
  use Phoxx\Core\Framework\Application;
  use Phoxx\Core\Framework\ServiceContainer;
  use Phoxx\Core\Http\Exceptions\ResponseException;
  use Phoxx\Core\Http\Response;
  use Phoxx\Core\Router\RouteContainer;

  use PHPUnit\Framework\TestCase;

  final class ApplicationTest extends TestCase
  {
    public static $headers = [];

    public static $headersSent = false;

    public function responseStatusProvider(): array
    {
      return [[200], [404], [500]];
    }

    public function setUp(): void
    {
      self::$headers = [];
      self::$headersSent = false;
    }

    public function testGetRouteContainer(): void
    {
      $application = new Application();

      $this->assertInstanceOf(RouteContainer::class, $application->getRouteContainer());
    }

    public function testGetServiceContainer(): void
    {
      $application = new Application();

      $this->assertInstanceOf(ServiceContainer::class, $application->getServiceContainer());
    }

    /**
     * @dataProvider responseStatusProvider
     */
    public function testSend(int $status): void
    {
      $application = new Application();

      $this->expectOutputString('RESPONSE');

      $application->send(new Response('RESPONSE', $status));

      $this->assertSame($status, http_response_code());
    }

    public function testSendHeaders(): void
    {
      $application = new Application();
      $application->send(new Response('RESPONSE', 200, [
        'HEADER_1' => 'VALUE_1',
        'HEADER_2' => 'VALUE_2'
      ]));

      $this->assertSame(200, http_response_code());
      $this->assertSame(self::$headers, [
        'HEADER_1: VALUE_1',
        'HEADER_2: VALUE_2'
      ]);
    }

    public function testSendAfterHeadersSent(): void
    {
      self::$headersSent = true;

      $application = new Application();

      $this->expectException(ResponseException::class);

      $application->send(new Response('RESPONSE'));
    }
  }
}

