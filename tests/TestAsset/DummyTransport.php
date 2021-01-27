<?php

declare(strict_types=1);

namespace Yiisoft\Mailer\SwiftMailer\Tests\TestAsset;

use Swift_DependencyContainer;
use Swift_Events_EventListener;
use Swift_Mime_SimpleMessage;
use Swift_RfcComplianceException;
use Swift_Transport_EsmtpTransport;

final class DummyTransport extends Swift_Transport_EsmtpTransport
{
    /**
     * @var Swift_Events_EventListener[]
     */
    public array $plugins = [];

    /**
     * @var Swift_Mime_SimpleMessage[]
     */
    public array $sentMessages = [];

    public function __construct()
    {
        call_user_func_array(
            [$this, 'Swift_Transport_EsmtpTransport::__construct'],
            Swift_DependencyContainer::getInstance()->createDependenciesFor('transport.smtp'),
        );
    }

    public function registerPlugin(Swift_Events_EventListener $plugin): void
    {
        $this->plugins[] = $plugin;
        parent::registerPlugin($plugin);
    }

    protected function getBufferParams(): array
    {
        return [];
    }

    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null): int
    {
        if (empty($message->getSubject())) {
            throw new Swift_RfcComplianceException('Subject is required.');
        }

        $this->sentMessages[] = $message;
        return 1;
    }

    public function isStarted(): bool
    {
        return true;
    }
}
