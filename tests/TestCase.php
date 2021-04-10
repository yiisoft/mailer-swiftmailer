<?php

declare(strict_types=1);

namespace Yiisoft\Mailer\SwiftMailer\Tests;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use ReflectionClass;
use Swift_Transport;
use Yiisoft\Di\Container;
use Yiisoft\EventDispatcher\Dispatcher\Dispatcher;
use Yiisoft\EventDispatcher\Provider\Provider;
use Yiisoft\Factory\Definitions\Reference;
use Yiisoft\Mailer\MailerInterface;
use Yiisoft\Mailer\MessageBodyRenderer;
use Yiisoft\Mailer\MessageBodyTemplate;
use Yiisoft\Mailer\MessageFactory;
use Yiisoft\Mailer\MessageFactoryInterface;
use Yiisoft\Mailer\SwiftMailer\Mailer;
use Yiisoft\Mailer\SwiftMailer\Message;
use Yiisoft\Mailer\SwiftMailer\Tests\TestAsset\DummyTransport;
use Yiisoft\View\View;

use function sys_get_temp_dir;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    private ?ContainerInterface $container = null;

    protected function setUp(): void
    {
        $this->getContainer();
    }

    protected function tearDown(): void
    {
        $this->container = null;
    }

    protected function get(string $id)
    {
        return $this->getContainer()->get($id);
    }

    /**
     * Gets an inaccessible object property.
     *
     * @param object $object
     * @param string $propertyName
     *
     * @return mixed
     */
    protected function getInaccessibleProperty(object $object, string $propertyName)
    {
        $class = new ReflectionClass($object);

        while (!$class->hasProperty($propertyName)) {
            $class = $class->getParentClass();
        }

        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $result = $property->getValue($object);
        $property->setAccessible(false);

        return $result;
    }

    private function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            $tempDir = sys_get_temp_dir();

            $this->container = new Container([
                View::class => [
                    'class' => View::class,
                    'constructor' => [
                        'basePath' => $tempDir,
                    ],
                ],

                MessageFactoryInterface::class => [
                    'class' => MessageFactory::class,
                    'constructor' => [
                        'class' => Message::class,
                    ],
                ],

                MessageBodyRenderer::class => [
                    'class' => MessageBodyRenderer::class,
                    'constructor' => [
                        'view' => Reference::to(View::class),
                        'template' => Reference::to(MessageBodyTemplate::class),
                    ],
                ],

                MessageBodyTemplate::class => [
                    'class' => MessageBodyTemplate::class,
                    'constructor' => [
                        'viewPath' => $tempDir,
                        'htmlLayout' => '',
                        'textLayout' => '',
                    ],
                ],

                MailerInterface::class => Mailer::class,
                LoggerInterface::class => NullLogger::class,
                Swift_Transport::class => DummyTransport::class,
                EventDispatcherInterface::class => Dispatcher::class,
                ListenerProviderInterface::class => Provider::class,
            ]);
        }

        return $this->container;
    }
}
