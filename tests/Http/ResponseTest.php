<?php declare(strict_types=1);

namespace Phoxx\Core\Tests\Http;

use Phoxx\Core\Http\Response;

use PHPUnit\Framework\TestCase;

final class ResponseTest extends TestCase
{
    public function testShouldCreateResponse(): void
    {
        $response = new Response('Content', Response::HTTP_OK, ['Header' => 'Value']);

        $this->assertSame('Content', $response->getContent());
        $this->assertSame(Response::HTTP_OK, $response->getStatus());
        $this->assertSame('Value', $response->getHeader('Header'));
        $this->assertSame(['Header' => 'Value'], $response->getHeaders());
    }

    public function testShouldSetHeader(): void
    {
        $response = new Response();
        $response->setHeader('Header', 'Value');

        $this->assertSame(['Header' => 'Value'], $response->getHeaders());
    }

    public function testShouldGetHeaderNull()
    {
        $response = new Response();

        $this->assertNull($response->getHeader('Invalid'));
    }
}