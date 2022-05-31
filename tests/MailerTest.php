<?php

declare(strict_types=1);

namespace Yiisoft\Mailer\SwiftMailer\Tests;

use Psr\EventDispatcher\EventDispatcherInterface;
use RuntimeException;
use Swift_Events_EventListener;
use Swift_Plugins_AntiFloodPlugin;
use Swift_Plugins_LoggerPlugin;
use Swift_Plugins_Loggers_EchoLogger;
use Swift_Transport;
use Yiisoft\Mailer\MessageBodyRenderer;
use Yiisoft\Mailer\MessageFactoryInterface;
use Yiisoft\Mailer\SwiftMailer\Mailer;
use Yiisoft\Mailer\SwiftMailer\Message;
use Yiisoft\Mailer\SwiftMailer\Tests\TestAsset\DummyTransport;

final class MailerTest extends TestCase
{
    public function testSetup(): void
    {
        $this->assertSame(
            $this->get(Swift_Transport::class),
            $this
                ->getInaccessibleProperty($this->createMailer(), 'swiftMailer')
                ->getTransport(),
        );
    }

    public function testSend(): void
    {
        $mailer = $this->createMailer();

        $message = (new Message())
            ->withSubject('Hi')
            ->withTo('to@example.com');

        $mailer->send($message);
        $transport = $this
            ->getInaccessibleProperty($this->createMailer(), 'swiftMailer')
            ->getTransport();
        $this->assertSame([$message->getSwiftMessage()], $transport->sentMessages);

        $invalidMsg = (new Message())
            ->withSubject('')
            ->withTo('to@example.com');

        $this->expectException(RuntimeException::class);
        $mailer->send($invalidMsg);
    }

    public function dataProviderPlugins(): array
    {
        return [
            [new Swift_Plugins_LoggerPlugin(new Swift_Plugins_Loggers_EchoLogger(false))],
            [
                new Swift_Plugins_LoggerPlugin(new Swift_Plugins_Loggers_EchoLogger(false)),
                new Swift_Plugins_AntiFloodPlugin(),
            ],
        ];
    }

    /**
     * @dataProvider dataProviderPlugins
     *
     * @param Swift_Events_EventListener ...$plugins
     */
    public function testConstructorWithPlugins(Swift_Events_EventListener ... $plugins): void
    {
        $transport = $this
            ->getInaccessibleProperty($this->createMailer($plugins), 'swiftMailer')
            ->getTransport();
        $this->assertInstanceOf(DummyTransport::class, $transport);

        foreach ($plugins as $plugin) {
            $this->assertContains($plugin, $transport->plugins);
        }
    }

    private function createMailer(array $plugins = []): Mailer
    {
        return new Mailer(
            $this->get(MessageFactoryInterface::class),
            $this->get(MessageBodyRenderer::class),
            $this->get(EventDispatcherInterface::class),
            $this->get(Swift_Transport::class),
            $plugins,
        );
    }
}
