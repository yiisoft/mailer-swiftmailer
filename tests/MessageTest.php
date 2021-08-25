<?php

declare(strict_types=1);

namespace Yiisoft\Mailer\SwiftMailer\Tests;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use RuntimeException;
use Swift_Message;
use Swift_Mime_Attachment;
use Swift_Mime_MimePart;
use Swift_Mime_SimpleMessage;
use Swift_Signer;
use Swift_Signers_DKIMSigner;
use Swift_Signers_DomainKeySigner;
use Yiisoft\Mailer\File;
use Yiisoft\Mailer\SwiftMailer\Message;

use function basename;
use function file_get_contents;
use function function_exists;
use function serialize;
use function substr_count;
use function unserialize;

final class MessageTest extends TestCase
{
    private Message $message;

    public function setUp(): void
    {
        parent::setUp();
        $this->message = new Message();
    }

    public function testDefaultGetters(): void
    {
        $this->assertInstanceOf(Swift_Message::class, $this->message->getSwiftMessage());
        $this->assertSame('utf-8', $this->message->getCharset());
        $this->assertSame('', $this->message->getFrom());
        $this->assertSame('', $this->message->getTo());
        $this->assertSame('', $this->message->getReplyTo());
        $this->assertSame('', $this->message->getCc());
        $this->assertSame('', $this->message->getBcc());
        $this->assertSame('', $this->message->getSubject());
        $this->assertSame('', $this->message->getTextBody());
        $this->assertSame('', $this->message->getHtmlBody());
        $this->assertSame('', $this->message->getReturnPath());
        $this->assertSame('', $this->message->getReadReceiptTo());
        $this->assertSame('', $this->message->getSender());
        $this->assertSame(Swift_Mime_SimpleMessage::PRIORITY_NORMAL, $this->message->getPriority());
        $this->assertSame([], $this->message->getHeader('header'));
        $this->assertNull($this->message->getError());
        $this->assertNull($this->message->getDate());
    }

    public function testSubject(): void
    {
        $subject = 'Test subject';
        $message = $this->message->withSubject($subject);
        $this->assertNotSame($message, $this->message);
        $this->assertSame($subject, $message->getSubject());
    }

    public function charsetDataProvider(): array
    {
        return [['utf-8'], ['iso-8859-2']];
    }

    /**
     * @dataProvider charsetDataProvider
     *
     * @param string $charset
     */
    public function testCharset(string $charset): void
    {
        $message = $this->message->withCharset($charset);
        $this->assertNotSame($message, $this->message);
        $this->assertSame($charset, $message->getCharset());
    }

    public function addressesDataProvider(): array
    {
        return [
            [
                'foo@example.com',
                ['foo@example.com' => null],
            ],
            [
                ['foo@example.com', 'bar@example.com'],
                ['foo@example.com' => null, 'bar@example.com' => null],
            ],
            [
                ['foo@example.com' => 'foo'],
                ['foo@example.com' => 'foo'],
            ],
            [
                ['foo@example.com' => 'foo', 'bar@example.com' => 'bar'],
                ['foo@example.com' => 'foo', 'bar@example.com' => 'bar'],
            ],
        ];
    }

    /**
     * @dataProvider addressesDataProvider
     *
     * @param array|string $from
     * @param array $expected
     */
    public function testFrom($from, array $expected): void
    {
        $message = $this->message->withFrom($from);
        $this->assertNotSame($message, $this->message);
        $this->assertSame($expected, $message->getFrom());
    }

    /**
     * @dataProvider addressesDataProvider
     *
     * @param array|string $to
     * @param array $expected
     */
    public function testTo($to, array $expected): void
    {
        $message = $this->message->withTo($to);
        $this->assertNotSame($message, $this->message);
        $this->assertSame($expected, $message->getTo());
    }

    /**
     * @dataProvider addressesDataProvider
     *
     * @param array|string $replyTo
     * @param array $expected
     */
    public function testReplyTo($replyTo, array $expected): void
    {
        $message = $this->message->withReplyTo($replyTo);
        $this->assertNotSame($message, $this->message);
        $this->assertSame($expected, $message->getReplyTo());
    }

    /**
     * @dataProvider addressesDataProvider
     *
     * @param array|string $cc
     * @param array $expected
     */
    public function testCc($cc, array $expected): void
    {
        $message = $this->message->withCc($cc);
        $this->assertNotSame($message, $this->message);
        $this->assertSame($expected, $message->getCc());
    }

    /**
     * @dataProvider addressesDataProvider
     *
     * @param array|string $bcc
     * @param array $expected
     */
    public function testBcc($bcc, array $expected): void
    {
        $message = $this->message->withBcc($bcc);
        $this->assertNotSame($message, $this->message);
        $this->assertSame($expected, $message->getBcc());
    }

    /**
     * @dataProvider addressesDataProvider
     *
     * @param array|string $readReceiptTo
     * @param array $expected
     */
    public function testReadReceiptTo($readReceiptTo, array $expected): void
    {
        $message = $this->message->withReadReceiptTo($readReceiptTo);
        $this->assertNotSame($message, $this->message);
        $this->assertSame($expected, $message->getReadReceiptTo());
    }

    public function testDate(): void
    {
        $date = new DateTime();
        $message = $this->message->withDate($date);

        $this->assertNotSame($message, $this->message);
        $this->assertNotSame($date, $message->getDate());
        $this->assertInstanceOf(DateTimeImmutable::class, $message->getDate());
        $this->assertSame($date->getTimestamp(), $message->getDate()->getTimestamp());
        $this->assertSame([$date->format(DateTimeInterface::RFC2822)], $message->getHeader('Date'));
    }

    public function priorityDataProvider(): array
    {
        return [
            [Swift_Mime_SimpleMessage::PRIORITY_HIGHEST],
            [Swift_Mime_SimpleMessage::PRIORITY_HIGH],
            [Swift_Mime_SimpleMessage::PRIORITY_NORMAL],
            [Swift_Mime_SimpleMessage::PRIORITY_LOW],
            [Swift_Mime_SimpleMessage::PRIORITY_LOWEST],
        ];
    }

    /**
     * @dataProvider priorityDataProvider
     *
     * @param int $priority
     */
    public function testPriority(int $priority): void
    {
        $message = $this->message->withPriority($priority);
        $this->assertNotSame($message, $this->message);
        $this->assertSame($priority, $message->getPriority());
    }

    public function testReturnPath(): void
    {
        $address = 'foo@exmaple.com';
        $message = $this->message->withReturnPath($address);
        $this->assertNotSame($message, $this->message);
        $this->assertSame($address, $message->getReturnPath());
    }

    public function testSender(): void
    {
        $address = 'foo@exmaple.com';
        $message = $this->message->withSender($address);

        $this->assertNotSame($message, $this->message);
        $this->assertSame($address, $message->getSender());
    }

    public function headerDataProvider(): array
    {
        return [
            ['X-Foo', 'Bar', ['Bar']],
            ['X-Fuzz', ['Bar', 'Baz'], ['Bar', 'Baz']],
        ];
    }

    /**
     * @dataProvider headerDataProvider
     *
     * @param string $name
     * @param array|string $value
     * @param array $expected
     */
    public function testHeader(string $name, $value, array $expected): void
    {
        $message = $this->message->withHeader($name, $value);
        $this->assertNotSame($message, $this->message);
        $this->assertSame($expected, $message->getHeader($name));
    }

    /**
     * @dataProvider headerDataProvider
     *
     * @param string $name
     * @param array|string $value
     * @param array $expected
     */
    public function testHeaders(string $name, $value, array $expected): void
    {
        $message = $this->message->withHeaders([$name => $value]);
        $this->assertNotSame($message, $this->message);
        $this->assertSame($expected, $message->getHeader($name));
    }

    public function testTextBody(): void
    {
        $body = 'Plain text';
        $message = $this->message->withTextBody($body);
        $this->assertNotSame($message, $this->message);
        $this->assertSame($body, $message->getTextBody());
    }

    public function testHtmlBody(): void
    {
        $body = '<p>HTML content</p>';
        $message = $this->message->withHtmlBody($body);
        $this->assertNotSame($message, $this->message);
        $this->assertSame($body, $message->getHtmlBody());
    }

    public function testError(): void
    {
        $this->assertNull($this->message->getError());

        $error = new Exception('Some error.');
        $message = $this->message->withError($error);

        $this->assertNotSame($message, $this->message);
        $this->assertSame($error, $message->getError());
    }

    public function testToString(): void
    {
        $charset = 'utf-16';
        $subject = 'Test Subject';
        $from = 'from@somedomain.com';
        $replyTo = 'reply-to@somedomain.com';
        $to = 'someuser@somedomain.com';
        $cc = 'ccuser@somedomain.com';
        $bcc = 'bccuser@somedomain.com';
        $returnPath = 'bounce@somedomain.com';
        $readReceiptTo = 'notify@somedomain.com';

        $messageString = $this->message
            ->withCharset($charset)
            ->withSubject($subject)
            ->withFrom($from)
            ->withReplyTo($replyTo)
            ->withTo($to)
            ->withCc($cc)
            ->withBcc($bcc)
            ->withReturnPath($returnPath)
            ->withPriority(2)
            ->withReadReceiptTo($readReceiptTo)
            ->__toString()
        ;

        $this->assertStringContainsString('charset=' . $charset, $messageString, 'Incorrect charset!');
        $this->assertStringContainsString('Subject: ' . $subject, $messageString, 'Incorrect "Subject" header!');
        $this->assertStringContainsString('From: ' . $from, $messageString, 'Incorrect "From" header!');
        $this->assertStringContainsString('Reply-To: ' . $replyTo, $messageString, 'Incorrect "Reply-To" header!');
        $this->assertStringContainsString('To: ' . $to, $messageString, 'Incorrect "To" header!');
        $this->assertStringContainsString('Cc: ' . $cc, $messageString, 'Incorrect "Cc" header!');
        $this->assertStringContainsString('Bcc: ' . $bcc, $messageString, 'Incorrect "Bcc" header!');
        $this->assertStringContainsString("Return-Path: <$returnPath>", $messageString, 'Incorrect "Return-Path" header!');
        $this->assertStringContainsString('X-Priority: 2 (High)', $messageString, 'Incorrect "Priority" header!');
        $this->assertStringContainsString('Disposition-Notification-To: ' . $readReceiptTo, $messageString, 'Incorrect "Disposition-Notification-To" header!');
    }

    public function testHeadersAndToString(): void
    {
        $messageString = $this->message
            ->withAddedHeader('Some', 'foo')
            ->withAddedHeader('Multiple', 'value1')
            ->withAddedHeader('Multiple', 'value2')
            ->__toString()
        ;

        $this->assertStringContainsString('Some: foo', $messageString, 'Unable to add header!');
        $this->assertStringContainsString('Multiple: value1', $messageString, 'First value of multiple header lost!');
        $this->assertStringContainsString('Multiple: value2', $messageString, 'Second value of multiple header lost!');

        $messageString = $this->message
            ->withHeader('Some', 'foo')
            ->withHeader('Some', 'override')
            ->withHeader('Multiple', ['value1', 'value2'])
            ->__toString()
        ;

        $this->assertStringContainsString('Some: override', $messageString, 'Unable to set header!');
        $this->assertStringNotContainsString('Some: foo', $messageString, 'Unable to override header!');
        $this->assertStringContainsString('Multiple: value1', $messageString, 'First value of multiple header lost!');
        $this->assertStringContainsString('Multiple: value2', $messageString, 'Second value of multiple header lost!');

        $message = $this->message->withHeader('Some', 'foo');
        $this->assertSame(['foo'], $message->getHeader('Some'));

        $newMessage = $message->withHeader('Multiple', ['value1', 'value2']);
        $this->assertNotSame($message, $newMessage);
        $this->assertSame(['value1', 'value2'], $newMessage->getHeader('Multiple'));

        $newMessage2 = $this->message->withHeaders(['Some' => 'foo', 'Multiple' => ['value1', 'value2']]);
        $this->assertSame(['foo'], $newMessage2->getHeader('Some'));
        $this->assertSame(['value1', 'value2'], $newMessage2->getHeader('Multiple'));
    }

    public function testSerialize(): void
    {
        $message = $this->message
            ->withTo('to@example.com')
            ->withFrom('someuser@somedomain.com')
            ->withSubject('Yii Swift Alternative Body Test')
            ->withTextBody('Yii Swift test plain text body')
        ;

        $this->assertNotSame($this->message, $message);

        $serializedMessage = serialize($message);
        $this->assertNotEmpty($serializedMessage, 'Unable to serialize message!');

        $unserializedMessaage = unserialize($serializedMessage);
        $this->assertEquals($message, $unserializedMessaage, 'Unable to unserialize message!');
    }

    public function testSetBodyWithSameContentType(): void
    {
        $message1 = $this->message->withHtmlBody('body1');
        $this->assertNotSame($this->message, $message1);
        $this->assertSame('body1', $message1->getHtmlBody());

        $message2 = $message1->withHtmlBody('body2');
        $this->assertNotSame($message1, $message2);
        $this->assertSame('body2', $message2->getHtmlBody());
    }

    public function testAlternativeBodyCharset(): void
    {
        $charset = 'windows-1251';
        $message = $this->message
            ->withCharset($charset)
            ->withTextBody('some text')
            ->withHtmlBody('some html')
        ;

        $this->assertNotSame($this->message, $message);
        $this->assertSame(2, substr_count((string) $message, $charset), 'Wrong charset for alternative body.');

        $message = $message->withTextBody('some text override');
        $this->assertSame(2, substr_count((string) $message, $charset), 'Wrong charset for alternative body override.');
    }

    public function testSendAlternativeBody()
    {
        $messageParts = $this->message
            ->withTo('to@example.com')
            ->withFrom('someuser@somedomain.com')
            ->withSubject('Yii Swift Alternative Body Test')
            ->withHtmlBody('<b>Yii Swift</b> test HTML body')
            ->withTextBody('Yii Swift test plain text body')
            ->getSwiftMessage()
            ->getChildren()
        ;

        $textPresent = false;
        $htmlPresent = false;

        foreach ($messageParts as $part) {
            if ($part instanceof Swift_Mime_MimePart) {
                if ($part->getContentType() == 'text/plain') {
                    $textPresent = true;
                }

                if ($part->getContentType() == 'text/html') {
                    $htmlPresent = true;
                }
            }
        }

        $this->assertTrue($textPresent, 'No text!');
        $this->assertTrue($htmlPresent, 'No HTML!');
    }

    public function testAttachFile(): void
    {
        $file = File::fromPath(__FILE__, 'test.php', 'application/x-php');

        $message = $this->message
            ->withTo('to@example.com')
            ->withFrom('someuser@somedomain.com')
            ->withSubject('Yii Swift Attach File Test')
            ->withTextBody('Yii Swift Attach File Test body')
            ->withAttached($file)
        ;
        $attachment = $this->getAttachment($message);

        $this->assertNotSame($this->message, $message);
        $this->assertIsObject($attachment, 'No attachment found!');
        $this->assertSame($attachment->getFilename(), $file->name(), 'Invalid file name!');
        $this->assertSame($file->contentType(), $attachment->getContentType(), 'Invalid content type!');
    }

    public function testAttachContent(): void
    {
        $file = File::fromContent('Test attachment content', 'test.txt', 'text/plain');

        $message = $this->message
            ->withTo('to@example.com')
            ->withFrom('someuser@somedomain.com')
            ->withSubject('Yii Swift Attach Content Test')
            ->withTextBody('Yii Swift Attach Content Test body')
            ->withAttached($file)
        ;
        $attachment = $this->getAttachment($message);

        $this->assertNotSame($this->message, $message);
        $this->assertIsObject($attachment, 'No attachment found!');
        $this->assertSame($attachment->getFilename(), $file->name(), 'Invalid file name!');
        $this->assertSame($file->contentType(), $attachment->getContentType(), 'Invalid content type!');
    }

    public function testEmbedFile(): void
    {
        $path = $this->createImageFile('embed-file.png', 'Embed Image File');
        $file = File::fromPath($path, basename($path), 'image/png');

        $message = $this->message
            ->withTo('to@example.com')
            ->withFrom('someuser@somedomain.com')
            ->withSubject('Yii Swift Embed File Test')
            ->withHtmlBody('Embed image: <img src="' . $file->cid() . '" alt="pic">')
            ->withEmbedded($file)
        ;
        $attachment = $this->getAttachment($message);

        $this->assertNotSame($this->message, $message);
        $this->assertIsObject($attachment, 'No attachment found!');
        $this->assertSame($attachment->getFilename(), $file->name(), 'Invalid file name!');
        $this->assertSame($file->contentType(), $attachment->getContentType(), 'Invalid content type!');
    }

    public function testEmbedContent(): void
    {
        $path = $this->createImageFile('embed-file.png', 'Embed Image File');
        $file = File::fromContent(file_get_contents($path), basename($path), 'image/png');

        $message = $this->message
            ->withTo('to@example.com')
            ->withFrom('someuser@somedomain.com')
            ->withSubject('Yii Swift Embed Content Test')
            ->withHtmlBody('Embed image: <img src="' . $file->cid() . '" alt="pic">')
            ->withEmbedded($file)
        ;
        $attachment = $this->getAttachment($message);

        $this->assertNotSame($this->message, $message);
        $this->assertIsObject($attachment, 'No attachment found!');
        $this->assertSame($file->name(), $attachment->getFilename(), 'Invalid file name!');
        $this->assertSame($file->contentType(), $attachment->getContentType(), 'Invalid content type!');
    }

    public function testImmutability(): void
    {
        $file = File::fromContent('Test attachment content', 'test.txt', 'text/plain');

        $message = clone $this->message;
        $this->assertNotSame($this->message->getSwiftMessage(), $message->getSwiftMessage());

        $this->assertNotSame($this->message, $this->message->withCharset('UTF-8'));
        $this->assertNotSame($this->message, $this->message->withFrom('from@example.com'));
        $this->assertNotSame($this->message, $this->message->withTo('to@example.com'));
        $this->assertNotSame($this->message, $this->message->withReplyTo('replyTo@example.com'));
        $this->assertNotSame($this->message, $this->message->withCc('cc@example.com'));
        $this->assertNotSame($this->message, $this->message->withBcc('bcc@example.com'));
        $this->assertNotSame($this->message, $this->message->withSubject(''));
        $this->assertNotSame($this->message, $this->message->withTextBody(''));
        $this->assertNotSame($this->message, $this->message->withHtmlBody(''));
        $this->assertNotSame($this->message, $this->message->withAttached($file));
        $this->assertNotSame($this->message, $this->message->withEmbedded($file));
        $this->assertNotSame($this->message, $this->message->withAddedHeader('name', 'value'));
        $this->assertNotSame($this->message, $this->message->withHeader('name', 'value'));
        $this->assertNotSame($this->message, $this->message->withHeaders([]));
        $this->assertNotSame($this->message, $this->message->withError(new RuntimeException()));
        $this->assertNotSame($this->message, $this->message->withDate(new DateTime()));
        $this->assertNotSame($this->message, $this->message->withPriority(1));
        $this->assertNotSame($this->message, $this->message->withReturnPath('bounce@example.com'));
        $this->assertNotSame($this->message, $this->message->withSender('sender@example.com'));
        $this->assertNotSame($this->message, $this->message->withReadReceiptTo('readReceiptTo@example.com'));
        $this->assertNotSame($this->message, $this->message->withAttachedSigners([]));
    }

    private function getAttachment(Message $message): ?Swift_Mime_Attachment
    {
        $messageParts = $message->getSwiftMessage()->getChildren();

        $attachment = null;

        foreach ($messageParts as $part) {
            if ($part instanceof Swift_Mime_Attachment) {
                $attachment = $part;
                break;
            }
        }

        return $attachment;
    }

    public function signerDataProvider(): array
    {
        $domain = 'example.com';
        $selector = 'default';
        $privateKey = '-----BEGIN RSA PRIVATE KEY-----
MIIEpAIBAAKCAQEAyehiMTRxvfQz8nbQQAgL481QipVMF+E7ljWKHTQQSYfqktR+
zFYqX81vKeK9/2D6AiK5KJSBVdF7aURasppuDaxFJWrPvacd3IQCrGxsGkwwlWPO
ggB1WpOEKhVUZnGzdm96Fk23oHFKrEiQlSG0cB9P/wUKz57b8tsaPve5sKBG0Kww
9YIDRM0x4w3c9fupPz8H5p2HHn4uPbn+whJyALZHD1+CftIGOHq8AUH4w4Z7bjF4
DD4zibpgRn96BVaRIJjxZdlKq69v52j3v8O8SAqSkWmpDWiIsm85Gl00Loay6iiJ
XNy11y0sUysFeCSpb/9cRyxb6j0jEwQXrw0J/QIDAQABAoIBAQCFuRgXeKGAalVh
V5mTXwDo7hlSv5C3HCBH2svPjaTf3lnYx033bXYBH2Fpf1fQ5NyQP4kcPEbwnJ48
2N2s/qS2/4qIPpa6CA259+CBbAmo3R8sQf8KkN0okRzudlQAyXtPjINydCSS6ZXI
RwMjEkCcJdDomOFRIuiPjtdyLsXYGRAa95yjpTU0ri1mEJocX6tlchlgUsjwc2ml
rCTKLc6b3KtYNYUZ/Rg0HzWRIhkbQFIz7uS0t7gF3sqDOLcaoWIv2rmrpg5T0suA
e5Sz7nK2XBeaPi/AKNCVoXJiCJ6SU6A+6Q4T5Rvnt+uxGpLKiilb/fRpQaq1RFO9
k5BDPgftAoGBAPyYBPrTPYPYGosqzbFypNaWLOUnjkdFxlThpwvLOa7nzwVcsQ8V
EXDkELNYy/jOYJLsNhhZ+bGAwWdNV46pdurFKuzS4vb11RfZCc3BTM05IFUFrKir
YVgWw5AYKJLkUiACASEP55P8j2cKocCV5SdI0sGyU7W+3S1NbhBOlr0nAoGBAMyh
Y/Ki5wo3LX43l9F1I2HnKVJSj2XzpWTSYco8sUbS4yUBVk9qPBjIHhT+mK2k2FqD
bSWsu5tGVfaMlFbYxXnSBqjIQfHRLWWVmWMr5sLFk0aJyY1mjGh6BEhTp/Xs86/w
cdVlI1N5blxPy4VvoLmHIb/O1xqi64FV1gW7gD47AoGAErFlXPKZENLDVB08z67+
R+shM2wz+U5OmSWB6TuG70y0Y18ysz0J52LZYYxmu+j5+KWGc1LlSZ+PsIdmvWYJ
KOKihJgut7wFoxgqw5FUj7N0kxYyauET+SLmIhnHludStI+xabL1nlwIeMWupsPx
C3E2N6Ns0nxnfdzHEmneee0CgYA5kF0RcIoV8Ze2neTzY0Rk0iZpphf40iWAyz3/
KjukdMa5LjsddAEb54+u0EAa+Phz3eziYEkWUR71kG5aT/idYFvHNy513CYtIXxY
zYzI1dOsUC6GvIZbDZgO0Jm7MMEMiVM8eIsLfGlzRm82RkSsbDsuPf183L/rTj46
tphI6QKBgQDobarzJhVUdME4QKAlhJecKBO1xlVCXWbKGdRcJn0Gzq6iwZKdx64C
hQGpKaZBDDCHLk7dDzoKXF1udriW9EcImh09uIKGYYWS8poy8NUzmZ3fy/1o2C2O
U41eAdnQ3dDGzUNedIJkSh6Z0A4VMZIEOag9hPNYqQXZBQgfobvPKw==
-----END RSA PRIVATE KEY-----
';
        return [
            [new Swift_Signers_DKIMSigner($privateKey, $domain, $selector)],
            [
                new Swift_Signers_DKIMSigner($privateKey, $domain, $selector),
                new Swift_Signers_DomainKeySigner($privateKey, $domain, $selector),
            ],
        ];
    }

    /**
     * @dataProvider signerDataProvider
     *
     * @param Swift_Signer ...$signers
     */
    public function testAttachSigners(Swift_Signer ...$signers): void
    {
        $message = $this->message->withAttachedSigners($signers);
        $this->assertNotSame($this->message, $message);
        $this->assertSame($signers, $this->getInaccessibleProperty($message->getSwiftMessage(), 'headerSigners'));
    }

    private function createImageFile(string $fileName = 'test.png', string $text = 'Test Image'): string
    {
        if (!function_exists('imagepng')) {
            $this->markTestSkipped('GD lib required.');
        }

        $fileFullName = $this->getTestFilePath() . DIRECTORY_SEPARATOR . $fileName;
        $image = \imagecreatetruecolor(120, 20);

        if ($image === false) {
            throw new RuntimeException('Unable create a new true color image');
        }

        $textColor = \imagecolorallocate($image, 233, 14, 91);
        \imagestring($image, 1, 5, 5, $text, $textColor);
        \imagepng($image, $fileFullName);
        \imagedestroy($image);

        return $fileFullName;
    }
}
