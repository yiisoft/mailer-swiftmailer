<?php

$_ENV['TEST_RUNTIME_PATH'] = $_ENV['TEST_RUNTIME_PATH'] ?? dirname(__DIR__) . '/runtime';

// Yii::setAlias('@yiiunit/swiftmailer', __DIR__);
// Yii::setAlias('@yii/swiftmailer', dirname(__DIR__) . '/src');
/*return [
    'app' => [
        'id' => 'testapp',
        'aliases' => [
            '@webroot'           => '@yiiunit/captcha/data/web',
            '@runtime'           => $_ENV['TEST_RUNTIME_PATH'],
            '@yii/tests/runtime' => $_ENV['TEST_RUNTIME_PATH'],
        ],
    ],
    'assetManager' => [
        '__class'   => yii\web\AssetManager::class,
        'basePath'  => '@webroot/assets',
        'baseUrl'   => '@web/assets',
    ],
    'session' => [
        '__class' => \yiiunit\captcha\data\Session::class,
    ],
];
*/
return [
    'app' => [
        'id' => 'testapp',
    ],
];
