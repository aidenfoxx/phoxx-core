<?php declare(strict_types=1);

namespace Phoxx\Core\Http
{
    function file_get_contents() {
        return 'content';
    }
}

namespace Phoxx\Core\Tests\Http
{
    use Phoxx\Core\Http\ServerRequest;

    use PHPUnit\Framework\TestCase;
    
    final class ServerRequestTest extends TestCase
    {
        public function testShouldCreateServerRequest()
        {
            $_GET['query'] = 'value';
            $_POST['request'] = 'value';
            $_SERVER['server'] = 'value';
            $_COOKIE['cookie'] = 'value';
            $_FILES['file'] = 'value';

            $request = new ServerRequest('path', 'method');
    
            $this->assertSame('path?query=value', $request->getUri());
            $this->assertSame('METHOD', $request->getMethod());
            $this->assertSame('value', $request->getQuery('query'));
            $this->assertSame('value', $request->getRequest('request'));
            $this->assertSame('value', $request->getServer('server'));
            $this->assertSame('value', $request->getCookie('cookie'));
            $this->assertSame(['value'], $request->getFile('file'));
            $this->assertSame('content', $request->getContent());
        }
    }
}
