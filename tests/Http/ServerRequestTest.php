<?php declare(strict_types=1);

namespace Phoxx\Core\Http
{
    function file_get_contents() {
        return 'CONTENT';
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
            $_GET['QUERY'] = 'VALUE';
            $_POST['REQUEST'] = 'VALUE';
            $_SERVER['SERVER'] = 'VALUE';
            $_COOKIE['COOKIE'] = 'VALUE';
            $_FILES['FILE'] = 'VALUE';

            $request = new ServerRequest('PATH', 'METHOD');
    
            $this->assertSame('PATH?QUERY=VALUE', $request->getUri());
            $this->assertSame('METHOD', $request->getMethod());
            $this->assertSame('VALUE', $request->getQuery('QUERY'));
            $this->assertSame('VALUE', $request->getRequest('REQUEST'));
            $this->assertSame('VALUE', $request->getServer('SERVER'));
            $this->assertSame('VALUE', $request->getCookie('COOKIE'));
            $this->assertSame(['VALUE'], $request->getFile('FILE'));
            $this->assertSame('CONTENT', $request->getContent());
        }
    }
}
