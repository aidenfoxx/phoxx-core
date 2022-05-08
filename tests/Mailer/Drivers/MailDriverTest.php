<?php declare(strict_types=1);

namespace Phoxx\Core\Mailer\Drivers
{
    final class MailTestHelper
    {
        public static $to;

        public static $subject;

        public static $content;

        public static $headers;

        public static $success;

        public static function clear()
        {
            self::$to = null;
            self::$subject = null;
            self::$content = null;
            self::$headers = null;
            self::$success = true;
        }
    }

    function mail($to, $subject, $content, $headers)
    {
        MailTestHelper::$to = $to;
        MailTestHelper::$subject = $subject;
        MailTestHelper::$content = $content;
        MailTestHelper::$headers = $headers;

        return MailTestHelper::$success;
    }
}

namespace Phoxx\Core\Tests\Mailer\Drivers
{
    use Phoxx\Core\Exceptions\MailException;
    use Phoxx\Core\Mailer\Drivers\MailDriver;
    use Phoxx\Core\Mailer\Drivers\MailTestHelper;
    use Phoxx\Core\Mailer\Mail;
    use Phoxx\Core\Renderer\Renderer;
    use Phoxx\Core\Renderer\View;

    use PHPUnit\Framework\TestCase;

    final class MailDriverTest extends TestCase
    {
        public function setUp(): void
        {
            MailTestHelper::clear();
        }

        public function testShouldSendMail()
        {
            $view = new View('template');
            $renderer = $this->createMock(Renderer::class);
            $renderer->expects($this->once())->method('render')->with($view)->willReturn('content');

            $mail = new Mail('subject', $view, 'john@test.com', 'john doe', ['header' => 'value']);
            $mail->addRecipient('jane@test.com', 'jane doe');

            $driver = new MailDriver($renderer);
            $driver->send($mail);

            $this->assertSame('jane doe <jane@test.com>', MailTestHelper::$to);
            $this->assertSame('subject', MailTestHelper::$subject);
            $this->assertSame('content', MailTestHelper::$content);
            $this->assertSame([
                'mime-version' => '1.0',
                'content-type' => 'text/html; charset=UTF-8',
                'header' => 'value',
                'from' => 'john doe <john@test.com>',
                'to' => 'jane doe <jane@test.com>'
            ], MailTestHelper::$headers);
        }

        public function testShouldSendMultipleRecipients()
        {
            $renderer = $this->createMock(Renderer::class);
            $renderer->expects($this->once())->method('render')->willReturn('content');

            $mail = new Mail('dubject', new View('template'), 'john@test.com');
            $mail->addRecipient('john@test.com');
            $mail->addRecipient('jane@test.com', 'jane doe');
            $mail->addCc('jack@test.com');
            $mail->addCc('jill@test.com', 'jill doe');
            $mail->addBcc('james@test.com');
            $mail->addBcc('joan@test.com', 'joan doe');

            $driver = new MailDriver($renderer);
            $driver->send($mail);

            $this->assertSame('john@test.com, jane doe <jane@test.com>', MailTestHelper::$to);
            $this->assertSame([
                'mime-version' => '1.0',
                'content-type' => 'text/html; charset=UTF-8',
                'from' => 'john@test.com',
                'to' => 'john@test.com, jane doe <jane@test.com>',
                'cc' => 'jack@test.com, jill doe <jill@test.com>',
                'bcc' => 'james@test.com, joan doe <joan@test.com>',
            ], MailTestHelper::$headers);
        }

        public function testShouldRejectNoRecipients()
        {
            $renderer = $this->createMock(Renderer::class);

            $mail = new Mail('subject', new View('template'));
            $driver = new MailDriver($renderer);

            $this->expectException(MailException::class);

            $driver->send($mail);
        }

        public function testShouldRejectMailError()
        {
            MailTestHelper::$success = false;

            $renderer = $this->createMock(Renderer::class);
            $renderer->expects($this->once())->method('render')->willReturn('content');
            
            $mail = new Mail('subject', new View('template'));
            $mail->addRecipient('john@test.com');

            $driver = new MailDriver($renderer);

            $this->expectException(MailException::class);
            
            $driver->send($mail);
        }
    }
}

