<?php declare(strict_types=1);

namespace Phoxx\Core\Tests\Http;

use Phoxx\Core\Http\Request;

use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase
{
    // TODO: Test full server like headers in Mail/View/MailDriver/Response?
    public function testShouldCreateRequest()
    {
        $request = new Request(
            'PATH',
            'METHOD',
            ['QUERY' => 'VALUE'],
            ['REQUEST' => 'VALUE'],
            ['SERVER' => 'VALUE'],
            ['COOKIE' => 'VALUE'],
            ['FILE' => 'VALUE'],
            'CONTENT'
        );

        $this->assertSame('PATH', $request->getPath());
        $this->assertSame('PATH?QUERY=VALUE', $request->getUri());
        $this->assertSame('METHOD', $request->getMethod());
        $this->assertSame('VALUE', $request->getQuery('QUERY'));
        $this->assertSame('VALUE', $request->getRequest('REQUEST'));
        $this->assertSame('VALUE', $request->getCookie('COOKIE'));
        $this->assertSame(['VALUE'], $request->getFile('FILE'));
        $this->assertSame('CONTENT', $request->getContent());
    }

    public function testShouldCreatePostRequest()
    {
        $request = new Request('PATH', 'POST');

        $this->assertSame('application/x-www-form-urlencoded', $request->getServer('CONTENT_TYPE'));
    }

    public function testShouldCreateExternalRequest()
    {
        $request = new Request('HTTP://TEST.COM');

        $this->assertSame('http://TEST.COM', $request->getUrl());

        $this->assertNull($request->getServer('HTTPS'));
        $this->assertSame(80, $request->getServer('SERVER_PORT'));
        $this->assertSame('TEST.COM', $request->getServer('HTTP_HOST'));
    }

    public function testShouldCreateExternalRequestWithPort()
    {
        $request = new Request('HTTP://TEST.COM:123');

        $this->assertSame('http://TEST.COM:123', $request->getUrl());

        $this->assertNull($request->getServer('HTTPS'));
        $this->assertSame(123, $request->getServer('SERVER_PORT'));
        $this->assertSame('TEST.COM:123', $request->getServer('HTTP_HOST'));
    }

    public function testShouldCreateSecureRequest()
    {
        $request = new Request('HTTPS://TEST.COM');

        $this->assertSame('https://TEST.COM', $request->getUrl());

        $this->assertSame('on', $request->getServer('HTTPS'));
        $this->assertSame(443, $request->getServer('SERVER_PORT'));
        $this->assertSame('TEST.COM', $request->getServer('HTTP_HOST'));
    }

    public function testShouldCreateSecureRequestWithPort()
    {
        $request = new Request('HTTPS://TEST.COM:123');

        $this->assertSame('https://TEST.COM:123', $request->getUrl());

        $this->assertSame('on', $request->getServer('HTTPS'));
        $this->assertSame(123, $request->getServer('SERVER_PORT'));
        $this->assertSame('TEST.COM:123', $request->getServer('HTTP_HOST'));
    }

    public function testShouldCreateAuthRequest()
    {
        $request = new Request('HTTP://USER:PASS@TEST.COM');

        $this->assertSame('USER', $request->getServer('PHP_AUTH_USER'));
        $this->assertSame('PASS', $request->getServer('PHP_AUTH_PW'));
    }

    public function testShouldParseQuery()
    {
        $request = new Request('?URL=VALUE');

        $this->assertSame('URL=VALUE', $request->getServer('QUERY_STRING'));
        $this->assertSame('VALUE', $request->getQuery('URL'));
    }

    public function testShouldCombineQuery()
    {
        $request = new Request('?URL=VALUE', 'METHOD', ['QUERY' => 'VALUE']);

        $this->assertSame('URL=VALUE&QUERY=VALUE', $request->getServer('QUERY_STRING'));
        $this->assertSame('VALUE', $request->getQuery('URL'));
        $this->assertSame('VALUE', $request->getQuery('QUERY'));
    }

    public function testShouldGetBasePath()
    {
        $request = new Request('/BASE/PATH', 'METHOD', [], [], ['SCRIPT_NAME' => '/BASE/SCRIPT']);

        $this->assertSame('/PATH', $request->getPath());
        $this->assertSame('/BASE', $request->getBasePath());
    }
}

