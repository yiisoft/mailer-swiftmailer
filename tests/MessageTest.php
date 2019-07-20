<?php
namespace Yiisoft\Mailer\SwiftMailer\Tests;

use Yiisoft\Mailer\SwiftMailer\Message;

class MessageTest extends TestCase
{
    private function createMessage(): Message
    {
        return new Message();
    }

    public function testSetUp(): void
    {
        $message = $this->createMessage();
        $this->assertInstanceOf(\Swift_Message::class, $message->getSwiftMessage());
    }

    /**
     * @dataProvider dataProviderSubjects
     */
    public function testSubject(string $subject): void
    {
        $message = $this->createMessage()
            ->setSubject($subject);
        $this->assertSame($subject, $message->getSubject());
    }

    public function dataProviderSubjects(): array
    {
        return [
            ['foo'],
            ['bar'],
        ];
    }

    /**
     * @dataProvider dataProviderCharsets
     */
    public function testCharset(string $charset): void
    {
        $message = $this->createMessage()
            ->setCharset($charset);
        $this->assertSame($charset, $message->getCharset());
    }

    public function dataProviderCharsets(): array
    {
        return [
            ['utf-8'],
            ['iso-8859-2'],
        ];
    }

    /**
     * @dataProvider dataProviderFrom
     */
    public function testFrom($from, $expected): void
    {
        $message = $this->createMessage()
            ->setFrom($from);
        $this->assertEquals($expected, $message->getFrom());
    }

    public function dataProviderFrom(): array
    {
        return [
            [
                'foo@example.com',
                ['foo@example.com' => null]
            ],
            [
                ['foo@example.com', 'bar@example.com'],
                ['foo@example.com' => null, 'bar@example.com' => null],
            ],
            [
                ['foo@example.com' => 'foo'],
                ['foo@example.com' => 'foo']
            ],
            [
                ['foo@example.com' => 'foo', 'bar@example.com' => 'bar'],
                ['foo@example.com' => 'foo', 'bar@example.com' => 'bar']
            ],
        ];
    }

    /**
     * @dataProvider dataProviderRecipients
     */
    public function testTo($to, $expected): void
    {
        $message = $this->createMessage()
            ->setTo($to);
        $this->assertEquals($expected, $message->getTo());
    }

    /**
     * @dataProvider dataProviderRecipients
     */
    public function testCc($cc, $expected): void
    {
        $message = $this->createMessage()
            ->setCc($cc);
        $this->assertEquals($expected, $message->getCc());
    }

    /**
     * @dataProvider dataProviderRecipients
     */
    public function testBcc($bcc, $expected): void
    {
        $message = $this->createMessage()
            ->setBcc($bcc);
        $this->assertEquals($expected, $message->getBcc());
    }

    /**
     * @dataProvider dataProviderRecipients
     */
    public function testReplyTo($to, $expected): void
    {
        $message = $this->createMessage()
            ->setReplyTo($to);
        $this->assertEquals($expected, $message->getReplyTo());
    }

    /**
     * @dataProvider dataProviderRecipients
     */
    public function testReadReceiptTo($to, $expected): void
    {
        $message = $this->createMessage()
            ->setReadReceiptTo($to);
        $this->assertEquals($expected, $message->getReadReceiptTo());
    }

    public function dataProviderRecipients(): array
    {
        return [
            [
                'foo@example.com',
                ['foo@example.com' => null]
            ],
            [
                ['foo@example.com', 'bar@example.com'],
                ['foo@example.com' => null, 'bar@example.com' => null],
            ],
            [
                ['foo@example.com' => 'foo'],
                ['foo@example.com' => 'foo']
            ],
            [
                ['foo@example.com' => 'foo', 'bar@example.com' => 'bar'],
                ['foo@example.com' => 'foo', 'bar@example.com' => 'bar']
            ],
        ];
    }

    public function testReturnPath(): void
    {
        $address = 'foo@exmaple.com';
        $message = $this->createMessage()->setReturnPath($address);
        $this->assertEquals($address, $message->getReturnPath());
    }

    /**
     * @dataProvider dataProviderPriorities
     */
    public function testPriority(int $priority): void
    {
        $message = $this->createMessage()->setPriority($priority);
        $this->assertEquals($priority, $message->getPriority());
    }

    public function dataProviderPriorities(): array
    {
        return [
            [\Swift_Mime_SimpleMessage::PRIORITY_HIGHEST],
            [\Swift_Mime_SimpleMessage::PRIORITY_HIGH],
            [\Swift_Mime_SimpleMessage::PRIORITY_NORMAL],
            [\Swift_Mime_SimpleMessage::PRIORITY_LOW],
            [\Swift_Mime_SimpleMessage::PRIORITY_LOWEST],
        ];
    }

    /**
     * @dataProvider dataProviderHeaders
     */
    public function testHeader(string $name, $value, $expected): void
    {
        $message = $this->createMessage();
        $this->assertEmpty($message->getHeader($name));
        $message->setHeader($name, $value);
        $this->assertEquals($expected, $message->getHeader($name));
    }

    public function dataProviderHeaders(): array
    {
        return [
            ['X-Foo', 'Bar', ['Bar']],
            ['X-Fuzz', ['Bar', 'Baz'], ['Bar', 'Baz']],
        ];
    }

    public function testTextBody(): void
    {
        $body = 'Dear foo';
        $message = $this->createMessage()
            ->setTextBody($body);
        $this->assertEquals($body, $message->getTextBody());
    }

    public function testHtmlBody(): void
    {
        $body = '<p>Dear foo</p>';
        $message = $this->createMessage()
            ->setHtmlBody($body);
        $this->assertEquals($body, $message->getHtmlBody());
    }

    public function testClone(): void
    {
        $m1 = new Message();
        $m1->setFrom('user@example.com');
        $m2 = clone $m1;
        $m1->setTo(['user1@example.com' => 'user1']);
        $m2->setTo(['user2@example.com' => 'user2']);

        $this->assertEquals(['user1@example.com' => 'user1'], $m1->getTo());
        $this->assertEquals(['user2@example.com' => 'user2'], $m2->getTo());

        $messageWithoutSwiftInitialized = new Message();
        $m2 = clone $messageWithoutSwiftInitialized; // should be no error during cloning
        $this->assertTrue($m2 instanceof Message);
    }

    public function testSetupHeaderShortcuts(): void
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

        $messageString = $this->createMessage()
            ->setCharset($charset)
            ->setSubject($subject)
            ->setFrom($from)
            ->setReplyTo($replyTo)
            ->setTo($to)
            ->setCc($cc)
            ->setBcc($bcc)
            ->setReturnPath($returnPath)
            ->setPriority(2)
            ->setReadReceiptTo($readReceiptTo)
            ->toString();

        $this->assertContains('charset=' . $charset, $messageString, 'Incorrect charset!');
        $this->assertContains('Subject: ' . $subject, $messageString, 'Incorrect "Subject" header!');
        $this->assertContains('From: ' . $from, $messageString, 'Incorrect "From" header!');
        $this->assertContains('Reply-To: ' . $replyTo, $messageString, 'Incorrect "Reply-To" header!');
        $this->assertContains('To: ' . $to, $messageString, 'Incorrect "To" header!');
        $this->assertContains('Cc: ' . $cc, $messageString, 'Incorrect "Cc" header!');
        $this->assertContains('Bcc: ' . $bcc, $messageString, 'Incorrect "Bcc" header!');
        $this->assertContains("Return-Path: <{$returnPath}>", $messageString, 'Incorrect "Return-Path" header!');
        $this->assertContains("X-Priority: 2 (High)", $messageString, 'Incorrect "Priority" header!');
        $this->assertContains('Disposition-Notification-To: ' . $readReceiptTo, $messageString, 'Incorrect "Disposition-Notification-To" header!');
    }
    
    public function testSetupHeaders(): void
    {
        $messageString = $this->createMessage()
            ->addHeader('Some', 'foo')
            ->addHeader('Multiple', 'value1')
            ->addHeader('Multiple', 'value2')
            ->toString();

        $this->assertContains('Some: foo', $messageString, 'Unable to add header!');
        $this->assertContains('Multiple: value1', $messageString, 'First value of multiple header lost!');
        $this->assertContains('Multiple: value2', $messageString, 'Second value of multiple header lost!');

        $messageString = $this->createMessage()
            ->setHeader('Some', 'foo')
            ->setHeader('Some', 'override')
            ->setHeader('Multiple', ['value1', 'value2'])
            ->toString();

        $this->assertContains('Some: override', $messageString, 'Unable to set header!');
        $this->assertNotContains('Some: foo', $messageString, 'Unable to override header!');
        $this->assertContains('Multiple: value1', $messageString, 'First value of multiple header lost!');
        $this->assertContains('Multiple: value2', $messageString, 'Second value of multiple header lost!');

        $message = $this->createMessage();
        $message->setHeader('Some', 'foo');
        $this->assertEquals(['foo'], $message->getHeader('Some'));
        $message->setHeader('Multiple', ['value1', 'value2']);
        $this->assertEquals(['value1', 'value2'], $message->getHeader('Multiple'));

        $message = $this->createMessage()
            ->setHeaders([
                'Some' => 'foo',
                'Multiple' => ['value1', 'value2'],
            ]);
        $this->assertEquals(['foo'], $message->getHeader('Some'));
        $this->assertEquals(['value1', 'value2'], $message->getHeader('Multiple'));
    }

    public function testSerialize(): void
    {
        $message = $this->createMessage()
            ->setTo('to@example.com')
            ->setFrom('someuser@somedomain.com')
            ->setSubject('Yii Swift Alternative Body Test')
            ->setTextBody('Yii Swift test plain text body');

        $serializedMessage = serialize($message);
        $this->assertNotEmpty($serializedMessage, 'Unable to serialize message!');

        $unserializedMessaage = unserialize($serializedMessage);
        $this->assertEquals($message, $unserializedMessaage, 'Unable to unserialize message!');
    }

    public function testSetBodyWithSameContentType()
    {
        $message = $this->createMessage();
        $message->setHtmlBody('body1');
        $message->setHtmlBody('body2');
        $this->assertEquals('body2', $message->getHtmlBody());
    }
    
    public function testAlternativeBodyCharset(): void
    {
        $message = $this->createMessage();
        $charset = 'windows-1251';
        $message->setCharset($charset);

        $message->setTextBody('some text');
        $message->setHtmlBody('some html');
        $content = $message->toString();
        $this->assertEquals(2, substr_count($content, $charset), 'Wrong charset for alternative body.');

        $message->setTextBody('some text override');
        $content = $message->toString();
        $this->assertEquals(2, substr_count($content, $charset), 'Wrong charset for alternative body override.');
    }
    
    public function testSendAlternativeBody()
    {
        $message = $this->createMessage()
            ->setTo('to@example.com')
            ->setFrom('someuser@somedomain.com')
            ->setSubject('Yii Swift Alternative Body Test')
            ->setHtmlBody('<b>Yii Swift</b> test HTML body')
            ->setTextBody('Yii Swift test plain text body');

        $messageParts = $message->getSwiftMessage()->getChildren();
        $textPresent = false;
        $htmlPresent = false;
        foreach ($messageParts as $part) {
            if (!($part instanceof \Swift_Mime_Attachment)) {
                /* @var $part \Swift_Mime_MimePart */
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

    public function testEmbedContent(): void
    {
        $fileFullName = $this->createImageFile('embed_file.jpg', 'Embed Image File');
        $message = $this->createMessage();

        $fileName = basename($fileFullName);
        $contentType = 'image/jpeg';
        $fileContent = file_get_contents($fileFullName);

        $cid = $message->embedContent($fileContent, ['fileName' => $fileName, 'contentType' => $contentType]);

        $message->setTo('to@example.com');
        $message->setFrom('someuser@somedomain.com');
        $message->setSubject('Yii Swift Embed File Test');
        $message->setHtmlBody('Embed image: <img src="' . $cid . '" alt="pic">');

        $attachment = $this->getAttachment($message);
        $this->assertTrue(is_object($attachment), 'No attachment found!');
        $this->assertEquals($fileName, $attachment->getFilename(), 'Invalid file name!');
        $this->assertEquals($contentType, $attachment->getContentType(), 'Invalid content type!');
    }
    
    public function testAttachFile(): void
    {
        $message = $this->createMessage();

        $message->setTo('to@example.com');
        $message->setFrom('someuser@somedomain.com');
        $message->setSubject('Yii Swift Attach File Test');
        $message->setTextBody('Yii Swift Attach File Test body');
        $fileName = __FILE__;
        $options = [
            'fileName' => $fileName,
            'contentType' => 'application/x-php',
        ];
        $message->attach($fileName, $options);

        $attachment = $this->getAttachment($message);
        $this->assertTrue(is_object($attachment), 'No attachment found!');
        $this->assertContains($attachment->getFilename(), $options['fileName'], 'Invalid file name!');
        $this->assertEquals($options['contentType'], $attachment->getContentType(), 'Invalid content type!');
    }

    public function testAttachContent(): void
    {
        $message = $this->createMessage();

        $message->setTo('to@example.com');
        $message->setFrom('someuser@somedomain.com');
        $message->setSubject('Yii Swift Create Attachment Test');
        $message->setTextBody('Yii Swift Create Attachment Test body');
        $fileName = 'test.txt';
        $fileContent = 'Test attachment content';
        $options = ['fileName' => $fileName, 'contentType' => 'text/plain'];
        $message->attachContent($fileContent, $options);

        $attachment = $this->getAttachment($message);
        $this->assertTrue(is_object($attachment), 'No attachment found!');
        $this->assertEquals($options['fileName'], $attachment->getFilename(), 'Invalid file name!');
        $this->assertEquals($options['contentType'], $attachment->getContentType(), 'Invalid content type!');
    }

    public function testEmbedFile(): void
    {
        $fileName = $this->createImageFile('embed_file.jpg', 'Embed Image File');

        $message = $this->createMessage();

        $options = ['fileName' => $fileName, 'contentType' => 'image/jpeg'];
        $cid = $message->embed($fileName, $options);

        $message->setTo('to@example.com');
        $message->setFrom('someuser@somedomain.com');
        $message->setSubject('Yii Swift Embed File Test');
        $message->setHtmlBody('Embed image: <img src="' . $cid. '" alt="pic">');

        $attachment = $this->getAttachment($message);
        $this->assertTrue(is_object($attachment), 'No attachment found!');
        $this->assertContains($attachment->getFilename(), $fileName, 'Invalid file name!');
        $this->assertEquals($options['fileName'], $attachment->getFilename(), 'Invalid file name!');
        $this->assertEquals($options['contentType'], $attachment->getContentType(), 'Invalid content type!');
    }

    /**
     * @dataProvider dataProviderSigners
     */
    public function testAttachSigners(\Swift_Signer ... $signers): void
    {
        $message = $this->createMessage();
        $message->attachSigners($signers);

        $property = new \ReflectionProperty(\Swift_Message::class, 'headerSigners');
        $property->setAccessible(true);
        $headerSigners = $property->getValue($message->getSwiftMessage());
        $this->assertEquals($signers, $headerSigners);
    }

    private $privateKey = "-----BEGIN RSA PRIVATE KEY-----
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
";

    public function dataProviderSigners(): array
    {
        $domain = 'example.com';
        $selector = 'default';

        return [
            [new \Swift_Signers_DKIMSigner($this->privateKey, $domain, $selector)],
            [new \Swift_Signers_DKIMSigner($this->privateKey, $domain, $selector), new \Swift_Signers_DomainKeySigner($this->privateKey, $domain, $selector)],
        ];
    }

    /**
     * Creates image file with given text.
     * @param  string $fileName file name.
     * @param  string $text     text to be applied on image.
     * @return string image file full name.
     */
    protected function createImageFile(string $fileName = 'test.jpg', string $text = 'Test Image'): string
    {
        if (!function_exists('imagejpeg')) {
            $this->markTestSkipped('GD lib required.');
        }
        $fileFullName = $this->getTestFilePath() . DIRECTORY_SEPARATOR . $fileName;
        $image = \imagecreatetruecolor(120, 20);
        if ($image === false) {
            throw new \RuntimeExceptio('Unable create a new true color image');
        }
        $textColor = \imagecolorallocate($image, 233, 14, 91);
        \imagestring($image, 1, 5, 5, $text, $textColor);
        \imagejpeg($image, $fileFullName);
        \imagedestroy($image);

        return $fileFullName;
    }

    /**
     * Finds the attachment object in the message.
     * @param  Message                     $message message instance
     * @return null|\Swift_Mime_Attachment attachment instance.
     */
    protected function getAttachment(Message $message)
    {
        $messageParts = $message->getSwiftMessage()->getChildren();
        $attachment = null;
        foreach ($messageParts as $part) {
            if ($part instanceof \Swift_Mime_Attachment) {
                $attachment = $part;
                break;
            }
        }

        return $attachment;
    }

    /**
     * @return string test file path.
     *
     * @throws \RuntimeException
     */
    protected function getTestFilePath(): string
    {
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR
            . str_replace('\\', '_', get_class($this)) . '_' . getmypid();
        
        if (!is_dir($dir) && mkdir($dir, 0777, true) === false) {
            throw new \RuntimeException('Unable to create temporary directory');
        }
        
        return $dir;
    }
}
