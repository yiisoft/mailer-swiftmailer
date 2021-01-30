<?php

declare(strict_types=1);

namespace Yiisoft\Mailer\SwiftMailer;

use Psr\EventDispatcher\EventDispatcherInterface;
use RuntimeException;
use Swift_Events_EventListener;
use Swift_Mailer;
use Swift_Transport;
use Yiisoft\Mailer\Mailer as BaseMailer;
use Yiisoft\Mailer\MessageBodyRenderer;
use Yiisoft\Mailer\MessageFactoryInterface;
use Yiisoft\Mailer\MessageInterface;

/**
 * Mailer implements a mailer based on SwiftMailer.
 *
 * @see https://swiftmailer.symfony.com
 */
final class Mailer extends BaseMailer
{
    private Swift_Mailer $swiftMailer;

    /**
     * @param MessageFactoryInterface $messageFactory
     * @param MessageBodyRenderer $messageBodyRenderer
     * @param EventDispatcherInterface $eventDispatcher
     * @param Swift_Transport $transport
     * @param Swift_Events_EventListener[] $plugins
     */
    public function __construct(
        MessageFactoryInterface $messageFactory,
        MessageBodyRenderer $messageBodyRenderer,
        EventDispatcherInterface $eventDispatcher,
        Swift_Transport $transport,
        array $plugins = []
    ) {
        parent::__construct($messageFactory, $messageBodyRenderer, $eventDispatcher);
        $this->swiftMailer = new Swift_Mailer($transport);

        foreach ($plugins as $plugin) {
            $this->swiftMailer->registerPlugin($plugin);
        }
    }

    protected function sendMessage(MessageInterface $message): void
    {
        /** @var Message $message */
        $sent = $this->swiftMailer->send($message->getSwiftMessage());

        if ($sent === 0) {
            throw new RuntimeException('Unable send message.');
        }
    }
}
