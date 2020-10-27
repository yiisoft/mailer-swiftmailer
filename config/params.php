<?php

declare(strict_types=1);

return [
    'yiisoft/mailer' => [
        'composer' => [
            'composerView' => dirname(__DIR__) . '/resources/mail'
        ],
        'fileMailer' => [
            'fileMailerStorage' => dirname(__DIR__) . '/runtime/mail'
        ],
        'writeToFiles' => true
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
