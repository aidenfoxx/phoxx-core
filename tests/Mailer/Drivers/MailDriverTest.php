<?php declare(strict_types=1);

namespace Phoxx\Core\Mailer\Drivers
{
    final class MailHelper {
        public static $to;

        public static $subject;

        public static $content;

        public static $headers;

        public static $success;
    }

    function mail(string $to, string $subject, string $content, array $headers)
    {
        MailHelper::$to = $to;
        MailHelper::$subject = $subject;
        MailHelper::$content = $content;
        MailHelper::$headers = $headers;

        return MailHelper::$success;
    }
}

namespace Phoxx\Core\Tests\Mailer\Drivers
{
    use Phoxx\Core\Exceptions\MailException;
    use Phoxx\Core\Mailer\Drivers\MailDriver;
    use Phoxx\Core\Mailer\Drivers\MailHelper;
    use Phoxx\Core\Mailer\Mail;
    use Phoxx\Core\Renderer\Renderer;
    use Phoxx\Core\Renderer\View;

    use PHPUnit\Framework\TestCase;

    final class MailDriverTest extends TestCase
    {
        public function setUp(): void
        {
            MailHelper::$to = null;
            MailHelper::$subject = null;
            MailHelper::$content = null;
            MailHelper::$headers = null;
            MailHelper::$success = true;
        }

        public function testShouldSendMail()
        {
            $view = new View('template');
            $renderer = $this->createMock(Renderer::class);
            $renderer->expects($this->once())->method('render')->with($view)->willReturn('Content');

            $mail = new Mail('Subject', $view, 'john@test.com', 'John Doe', ['Header' => 'Value']);
            $mail->addRecipient('jane@test.com', 'Jane Doe');

            $driver = new MailDriver($renderer);
            $driver->send($mail);

            $this->assertSame('Jane Doe <jane@test.com>', MailHelper::$to);
            $this->assertSame('Subject', MailHelper::$subject);
            $this->assertSame('Content', MailHelper::$content);
            $this->assertSame([
                'mime-version' => '1.0',
                'content-type' => 'text/html; charset=UTF-8',
                'header' => 'Value',
                'from' => 'John Doe <john@test.com>',
                'to' => 'Jane Doe <jane@test.com>'
            ], MailHelper::$headers);
        }

        public function testShouldSendMultipleRecipients()
        {
            $renderer = $this->createMock(Renderer::class);
            $renderer->expects($this->once())->method('render')->willReturn('Content');

            $mail = new Mail('Subject', new View('template'), 'john@test.com');
            $mail->addRecipient('john@test.com');
            $mail->addRecipient('jane@test.com', 'Jane Doe');
            $mail->addCc('jack@test.com');
            $mail->addCc('jill@test.com', 'Jill Doe');
            $mail->addBcc('james@test.com');
            $mail->addBcc('joan@test.com', 'Joan Doe');

            $driver = new MailDriver($renderer);
            $driver->send($mail);

            $this->assertSame('john@test.com, Jane Doe <jane@test.com>', MailHelper::$to);
            $this->assertSame([
                'mime-version' => '1.0',
                'content-type' => 'text/html; charset=UTF-8',
                'from' => 'john@test.com',
                'to' => 'john@test.com, Jane Doe <jane@test.com>',
                'cc' => 'jack@test.com, Jill Doe <jill@test.com>',
                'bcc' => 'james@test.com, Joan Doe <joan@test.com>',
            ], MailHelper::$headers);
        }

        public function testShouldRejectNoRecipients()
        {
            $renderer = $this->createMock(Renderer::class);

            $mail = new Mail('Subject', new View('template'));
            $driver = new MailDriver($renderer);

            $this->expectException(MailException::class);

            $driver->send($mail);
        }

        public function testShouldRejectMailError()
        {
            MailHelper::$success = false;

            $renderer = $this->createMock(Renderer::class);
            $renderer->expects($this->once())->method('render')->willReturn('Content');
            
            $mail = new Mail('Subject', new View('template'));
            $mail->addRecipient('john@test.com');

            $driver = new MailDriver($renderer);

            $this->expectException(MailException::class);
            
            $driver->send($mail);
        }
    }
}

