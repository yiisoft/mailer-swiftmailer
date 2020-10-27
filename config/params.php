<?php

declare(strict_types=1);

return [
    'yiisoft/mailer' => [
        'mailer' => [
            'composerView' => dirname(__DIR__) . '/resources/mail',
            'fileMailerStorage' => dirname(__DIR__) . '/runtime/mail',
            'writeToFiles' => true
        ]
    ],
    'swiftmailer/swiftmailer' => [
        'SwiftSmtpTransport' => [
            'host' => 'smtp.example.com',
            'port' => 25,
            'encryption' => null,
            'username' => 'admin@example.com',
            'password' => ''
        ]
    ]
];
