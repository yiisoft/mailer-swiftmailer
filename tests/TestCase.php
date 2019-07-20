<?php
namespace Yiisoft\Mailer\SwiftMailer\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Container\ContainerInterface;
use Yiisoft\Di\Container;
use Yiisoft\Mailer\MailerInterface;
use Yiisoft\Mailer\SwiftMailer\Mailer;

abstract class TestCase extends BaseTestCase
{
    /**
     * @var ContainerInterface $container
     */
    private $container;

    protected function setUp()
    {
        parent::setUp();
        $config = require __DIR__ . '/config.php';
        $this->container = new Container($config);
    }

    protected function tearDown()
    {
        $this->container = null;
        parent::tearDown();
    }

    protected function get($id)
    {
        return $this->container->get($id);
    }

    /**
     * @return Mailer mailer instance.
     */
    protected function getMailer(): Mailer
    {
        return $this->get(MailerInterface::class);
    }
}
