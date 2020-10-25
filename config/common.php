<?php

declare(strict_types=1);

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Factory\Definitions\Reference;
use Yiisoft\Mailer\Composer;
use Yiisoft\Mailer\FileMailer;
use Yiisoft\Mailer\MailerInterface;
use Yiisoft\Mailer\MessageFactory;
use Yiisoft\Mailer\MessageFactoryInterface;
use Yiisoft\Mailer\SwiftMailer\Logger;
use Yiisoft\Mailer\SwiftMailer\Mailer;
use Yiisoft\Mailer\SwiftMailer\Message;
use Yiisoft\View\WebView;

/** @var array $params */

return [
    Composer::class => [
        '__class' => Composer::class,
        '__construct()' => [
            Reference::to(WebView::class),
            $params['yiisoft/mailer']['mailerInterface']['composerView']
        ]
    ],

    MessageFactory::class => [
        '__class' => MessageFactory::class,
        '__construct()' => [
            Message::class
        ]
    ],

    MessageFactoryInterface::class => MessageFactory::class,

    Logger::class => [
        '__class' => Logger::class,
        '__construct()' => [Reference::to(LoggerInterface::class)]
    ],

    Swift_SmtpTransport::class => [
        '__class' => Swift_SmtpTransport::class,
        '__construct()' => [
            $params['yiisoft/mailer']['swiftSmtpTransport']['host'],
            $params['yiisoft/mailer']['swiftSmtpTransport']['port'],
            $params['yiisoft/mailer']['swiftSmtpTransport']['encryption']
        ],
        'setUsername()' => [$params['yiisoft/mailer']['swiftSmtpTransport']['username']],
        'setPassword()' => [$params['yiisoft/mailer']['swiftSmtpTransport']['password']]
    ],

    Swift_Plugins_LoggerPlugin::class => [
        '__class' => Swift_Plugins_LoggerPlugin::class,
        '__construct()' => [Reference::to(Logger::class)]
    ],

    Mailer::class => [
        '__class' => Mailer::class,
        '__construct()' => [
            Reference::to(MessageFactoryInterface::class),
            Reference::to(Composer::class),
            Reference::to(EventDispatcherInterface::class),
            Reference::to(LoggerInterface::class),
            Reference::to(Swift_SmtpTransport::class)
        ],
        'registerPlugin()' => [Reference::to(Swift_Plugins_LoggerPlugin::class)]
    ],

    FileMailer::class => [
        '__class' => FileMailer::class,
        '__construct()' => [
            Reference::to(MessageFactoryInterface::class),
            Reference::to(Composer::class),
            Reference::to(EventDispatcherInterface::class),
            Reference::to(LoggerInterface::class),
            $params['yiisoft/mailer']['mailerInterface']['fileMailerStorage']
        ]
    ],

    MailerInterface::class => $params['yiisoft/mailer']['mailerInterface']['writeToFiles']
        ? FileMailer::class : Mailer::class
];
