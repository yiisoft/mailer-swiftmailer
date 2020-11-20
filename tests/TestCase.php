<?php

declare(strict_types=1);

namespace Yiisoft\Mailer\SwiftMailer\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Container\ContainerInterface;
use Yiisoft\Di\Container;
use Yiisoft\Mailer\MailerInterface;
use Yiisoft\Mailer\SwiftMailer\Mailer;

abstract class TestCase extends BaseTestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;

    protected function setUp(): void
    {
        parent::setUp();
        $config = require __DIR__ . '/config.php';
        $this->container = new Container($config);
    }

    protected function tearDown(): void
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

    /**
     * Gets an inaccessible object property.
     *
     * @param $object
     * @param $propertyName
     * @param bool $revoke whether to make property inaccessible after getting
     *
     * @throws \ReflectionException
     *
     * @return mixed
     */
    protected function getInaccessibleProperty($object, $propertyName, bool $revoke = true)
    {
        $class = new \ReflectionClass($object);
        while (!$class->hasProperty($propertyName)) {
            $class = $class->getParentClass();
        }
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $result = $property->getValue($object);
        if ($revoke) {
            $property->setAccessible(false);
        }

        return $result;
    }
}
