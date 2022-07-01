<?php declare(strict_types=1);

namespace Phoxx\Core\Tests\Http;

use Phoxx\Core\Http\Request;

use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase
{
    public function testShouldCreateRequest()
    {
        $request = new Request(
            'path',
            'method',
            ['query' => 'value'],
            ['request' => 'value'],
            ['server' => 'value'],
            ['cookie' => 'value'],
            ['file' => 'value'],
            'content'
        );

        $this->assertSame('path', $request->getPath());
        $this->assertSame('path?query=value', $request->getUri());
        $this->assertSame('METHOD', $request->getMethod());
        $this->assertSame('value', $request->getQuery('query'));
        $this->assertSame('value', $request->getRequest('request'));
        $this->assertSame('value', $request->getCookie('cookie'));
        $this->assertSame(['value'], $request->getFile('file'));
        $this->assertSame('content', $request->getContent());
    }

    public function testShouldCreatePostRequest()
    {
        $request = new Request('path', 'POST');

        $this->assertSame('application/x-www-form-urlencoded', $request->getServer('CONTENT_TYPE'));
    }

    public function testShouldCreateExternalRequest()
    {
        $request = new Request('http://test.com');

        $this->assertNull($request->getServer('HTTPS'));
        $this->assertSame(80, $request->getServer('SERVER_PORT'));
        $this->assertSame('test.com', $request->getServer('HTTP_HOST'));
        
        $this->assertSame('http://test.com', $request->getUrl());
    }

    public function testShouldCreateExternalRequestWithPort()
    {
        $request = new Request('http://test.com:123');

        $this->assertNull($request->getServer('HTTPS'));
        $this->assertSame(123, $request->getServer('SERVER_PORT'));
        $this->assertSame('test.com:123', $request->getServer('HTTP_HOST'));

        $this->assertSame('http://test.com:123', $request->getUrl());
    }

    public function testShouldCreateSecureRequest()
    {
        $request = new Request('https://test.com');

        $this->assertSame('on', $request->getServer('HTTPS'));
        $this->assertSame(443, $request->getServer('SERVER_PORT'));
        $this->assertSame('test.com', $request->getServer('HTTP_HOST'));

        $this->assertSame('https://test.com', $request->getUrl());
    }

    public function testShouldCreateSecureRequestWithPort()
    {
        $request = new Request('https://test.com:123');

        $this->assertSame('on', $request->getServer('HTTPS'));
        $this->assertSame(123, $request->getServer('SERVER_PORT'));
        $this->assertSame('test.com:123', $request->getServer('HTTP_HOST'));

        $this->assertSame('https://test.com:123', $request->getUrl());
    }

    public function testShouldCreateAuthRequest()
    {
        $request = new Request('http://user:pass@test.com');

        $this->assertSame('user', $request->getServer('PHP_AUTH_USER'));
        $this->assertSame('pass', $request->getServer('PHP_AUTH_PW'));
    }

    public function testShouldParseQuery()
    {
        $request = new Request('?url=value');

        $this->assertSame('url=value', $request->getServer('QUERY_STRING'));
        $this->assertSame('value', $request->getQuery('url'));
    }

    public function testShouldCombineQuery()
    {
        $request = new Request('?url=value', 'method', ['query' => 'value']);

        $this->assertSame('url=value&query=value', $request->getServer('QUERY_STRING'));
        $this->assertSame('value', $request->getQuery('url'));
        $this->assertSame('value', $request->getQuery('query'));
    }

    public function testShouldGetBasePath()
    {
        $request = new Request('/base/path', 'method', [], [], ['SCRIPT_NAME' => '/base/script']);

        $this->assertSame('/path', $request->getPath());
        $this->assertSame('/base', $request->getBasePath());
    }
}

