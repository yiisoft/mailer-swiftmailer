<?php
namespace Yiisoft\Mailer\SwiftMailer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Mailer\BaseMailer;
use Yiisoft\Mailer\Composer;
use Yiisoft\Mailer\MessageFactoryInterface;
use Yiisoft\Mailer\MessageInterface;

/**
 * Mailer implements a mailer based on SwiftMailer.
 *
 * @see http://swiftmailer.org
 */
class Mailer extends BaseMailer
{
    /**
     * @var \Swift_Mailer Swift mailer instance.
     */
    private $swiftMailer;

    /**
     * Returns transport instance.
     *
     * @return \Swift_Transport
     */
    public function getTransport(): \Swift_Transport
    {
        return $this->swiftMailer->getTransport();
    }

    /**
     * @param MessageFactoryInterface $messageFactory
     * @param Composer $composer
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface $logger
     * @param \Swift_Transport $transport
     */
    public function __construct(
        MessageFactoryInterface $messageFactory,
        Composer $composer,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger,
        \Swift_Transport $transport
    ) {
        parent::__construct($messageFactory, $composer, $eventDispatcher, $logger);
        $this->swiftMailer = new \Swift_Mailer($transport);
    }

    protected function sendMessage(MessageInterface $message): void
    {
        /** @var Message $message */
        $sent = $this->swiftMailer->send($message->getSwiftMessage());
        if ($sent === 0) {
            throw new \RuntimeException('Unable send message');
        }
    }

    /**
     * Registers plugins.
     *
     * @param \Swift_Events_EventListener[] $plugins
     *
     * @return self
     */
    public function registerPlugins(array $plugins): self
    {
        foreach ($plugins as $plugin) {
            $this->registerPlugin($plugin);
        }

        return $this;
    }

    /**
     * Registers plugin.
     *
     * @see \Swift_Mailer::registerPlugins
     *
     * @return self
     */
    public function registerPlugin(\Swift_Events_EventListener $plugin): self
    {
        $this->swiftMailer->registerPlugin($plugin);

        return $this;
    }
}
