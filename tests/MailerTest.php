<?php

namespace yiiunit\swiftmailer;

use yii\helpers\Yii;
use yii\swiftmailer\Mailer;

Yii::setAlias('@yii/swiftmailer', __DIR__ . '/../../../../extensions/swiftmailer');

class MailerTest extends \yii\tests\TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->mockApplication();
    }

    // Tests :

    public function testSetupTransport()
    {
        $mailer = new Mailer($this->app);

        $transport = new \Swift_SendmailTransport();
        $mailer->setTransport($transport);
        $this->assertEquals($transport, $mailer->getTransport(), 'Unable to setup transport!');
    }

    /**
     * @depends testSetupTransport
     */
    public function testConfigureTransport()
    {
        $mailer = new Mailer($this->app);

        $transportConfig = [
            '__class' => \Swift_SmtpTransport::class,
            'host' => 'localhost',
            'username' => 'username',
            'password' => 'password',
        ];
        $mailer->setTransport($transportConfig);
        $transport = $mailer->getTransport();
        $this->assertTrue(is_object($transport), 'Unable to setup transport via config!');
        $this->assertEquals($transportConfig['__class'], get_class($transport), 'Invalid transport class!');
        $this->assertEquals($transportConfig['host'], $transport->getHost(), 'Invalid transport host!');
    }

    /**
     * @depends testConfigureTransport
     */
    public function testConfigureTransportConstruct()
    {
        $mailer = new Mailer($this->app);

        $class = \Swift_SmtpTransport::class;
        $host = 'some.test.host';
        $port = 999;
        $transportConfig = [
            '__class' => $class,
            '__construct()' => [
                $host,
                $port,
            ],
        ];
        $mailer->setTransport($transportConfig);
        $transport = $mailer->getTransport();

        $this->assertTrue(is_object($transport), 'Unable to setup transport via config!');
        $this->assertEquals($class, get_class($transport), 'Invalid transport class!');
        $this->assertEquals($host, $transport->getHost(), 'Invalid transport host!');
        $this->assertEquals($port, $transport->getPort(), 'Invalid transport host!');
    }

    /**
     * @depends testConfigureTransportConstruct
     */
    public function testConfigureTransportWithPlugins()
    {
        $mailer = new Mailer($this->app);

        $pluginClass = \Swift_Plugins_ThrottlerPlugin::class;
        $rate = 10;

        $transportConfig = [
            '__class' => \Swift_SmtpTransport::class,
            'plugins' => [
                [
                    '__class' => $pluginClass,
                    '__construct()' => [
                        $rate,
                    ],
                ],
            ],
        ];
        $mailer->setTransport($transportConfig);
        $transport = $mailer->getTransport();
        $this->assertTrue(is_object($transport), 'Unable to setup transport via config!');
        $this->assertContains(':' . $pluginClass . ':', print_r($transport, true), 'Plugin not added');
    }

    public function testGetSwiftMailer()
    {
        $mailer = new Mailer($this->app);
        $this->assertTrue(is_object($mailer->getSwiftMailer()), 'Unable to get Swift mailer instance!');
    }
}
