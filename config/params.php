<?php

declare(strict_types=1);

return [
    'yiisoft/mailer' => [
        'mailerInterface' => [
            'composerView' => dirname(__DIR__) . '/resources/mail',
            'fileMailerStorage' => dirname(__DIR__) . '/runtime/mail',
            'writeToFiles' => true
        ],
        'swiftSmtpTransport' => [
            'host' => 'smtp.example.com',
            'port' => 25,
            'encryption' => null,
            'username' => 'admin@example.com',
            'password' => ''
        ]
    ]
];
