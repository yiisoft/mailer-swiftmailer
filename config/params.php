<?php

declare(strict_types=1);

return [
    'yiisoft/mailer' => [
        'composerView' => dirname(__DIR__) . '/resources/mail',
        'fileMailerStorage' => dirname(__DIR__) . '/runtime/mail',
        'writeToFiles' => true
    ]
];
