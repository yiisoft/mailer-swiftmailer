<?php
namespace Yiisoft\Mailer\SwiftMailer\Tests;

use Yiisoft\Mailer\SwiftMailer\Message;

class MailerTest extends TestCase
{
    public function testSetUp(): void
    {
        $mailer = $this->getMailer();
        $this->assertEquals($this->get(TestTransport::class), $mailer->getTransport());
    }
    
    public function testSend(): void
    {
        $mailer = $this->getMailer();
        $message = (new Message())
            ->setSubject('Hi')
            ->setTo('to@example.com');
        $this->assertNull($mailer->send($message));

        $invalidMsg = (new Message())
            ->setSubject('')
            ->setTo('to@example.com');
        $this->expectException(\RuntimeException::class);
        $mailer->send($invalidMsg);
    }
    
    /**
     * @dataProvider dataProviderPlugins
     */
    public function testRegisterPlugins(\Swift_Events_EventListener ... $plugins): void
    {
        $mailer = $this->getMailer();
        $mailer->registerPlugins($plugins);

        $transport = $mailer->getTransport();
        $this->assertInstanceOf(TestTransport::class, $transport);

        foreach ($plugins as $plugin) {
            $this->assertContains($plugin, $transport->plugins);
        }
    }

    public function dataProviderPlugins(): array
    {
        return [
            [new \Swift_Plugins_LoggerPlugin(new \Swift_Plugins_Loggers_EchoLogger(false))],
            [
                new \Swift_Plugins_LoggerPlugin(new \Swift_Plugins_Loggers_EchoLogger(false)),
                new \Swift_Plugins_AntiFloodPlugin(),
            ],
        ];
    }
}
