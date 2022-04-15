<?php declare(strict_types=1);

namespace Phoxx\Core\Tests\Http;

use Phoxx\Core\Http\Response;

use PHPUnit\Framework\TestCase;

final class ResponseTest extends TestCase
{
    public function testShouldCreateResponse(): void
    {
        $response = new Response('content', Response::HTTP_OK, ['header' => 'value']);

        $this->assertSame('content', $response->getContent());
        $this->assertSame(Response::HTTP_OK, $response->getStatus());
        $this->assertSame('value', $response->getHeader('header'));
        $this->assertSame(['header' => 'value'], $response->getHeaders());
    }

    public function testShouldSetHeader(): void
    {
        $response = new Response();
        $response->setHeader('header', 'value');

        $this->assertSame(['header' => 'value'], $response->getHeaders());
    }

    public function testShouldGetHeaderNull()
    {
        $response = new Response();

        $this->assertNull($response->getHeader('invalid'));
    }
}