<?php

namespace yiiunit\swiftmailer;

use Yii;
use yii\swiftmailer\Mailer;

Yii::setAlias('@yii/swiftmailer', __DIR__ . '/../../../../extensions/swiftmailer');

class MailerTest extends TestCase
{
    public function setUp()
    {
        $this->mockApplication([
            'components' => [
                'email' => $this->createTestEmailComponent()
            ]
        ]);
    }

    /**
     * @return Mailer test email component instance.
     */
    protected function createTestEmailComponent()
    {
        $component = new Mailer();

        return $component;
    }

    // Tests :

    public function testSetupTransport()
    {
        $mailer = new Mailer();

        $transport = new \Swift_SendmailTransport();
        $mailer->setTransport($transport);
        $this->assertEquals($transport, $mailer->getTransport(), 'Unable to setup transport!');
    }

    /**
     * @depends testSetupTransport
     */
    public function testConfigureTransport()
    {
        $mailer = new Mailer();

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
        $mailer = new Mailer();

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
        $mailer = new Mailer();

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
        $mailer = new Mailer();
        $this->assertTrue(is_object($mailer->getSwiftMailer()), 'Unable to get Swift mailer instance!');
    }
}
