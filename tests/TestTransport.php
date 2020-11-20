<?php

declare(strict_types=1);

namespace Yiisoft\Mailer\SwiftMailer\Tests;

class TestTransport extends \Swift_Transport_EsmtpTransport
{
    /**
     * @var Swift_Events_EventListener[]
     */
    public $plugins;

    public function __construct()
    {
        call_user_func_array(
            [$this, 'Swift_Transport_EsmtpTransport::__construct'],
            \Swift_DependencyContainer::getInstance()->createDependenciesFor('transport.smtp')
        );
    }

    public function registerPlugin(\Swift_Events_EventListener $plugin)
    {
        $this->plugins[] = $plugin;
        parent::registerPlugin($plugin);
    }

    protected function getBufferParams()
    {
        return [];
    }

    public function send(\Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        if (empty($message->getSubject())) {
            throw new \Swift_RfcComplianceException('Subject is required');
        }

        return 1;
    }

    public function isStarted()
    {
        return true;
    }
}
