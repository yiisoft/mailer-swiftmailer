<?php

declare(strict_types=1);

namespace Yiisoft\Mailer\SwiftMailer\Tests;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use ReflectionClass;
use Swift_Transport;
use Yiisoft\Files\FileHelper;
use Yiisoft\Mailer\MailerInterface;
use Yiisoft\Mailer\MessageBodyRenderer;
use Yiisoft\Mailer\MessageBodyTemplate;
use Yiisoft\Mailer\MessageFactory;
use Yiisoft\Mailer\MessageFactoryInterface;
use Yiisoft\Mailer\SwiftMailer\Mailer;
use Yiisoft\Mailer\SwiftMailer\Message;
use Yiisoft\Mailer\SwiftMailer\Tests\TestAsset\DummyTransport;
use Yiisoft\Test\Support\Container\SimpleContainer;
use Yiisoft\Test\Support\EventDispatcher\SimpleEventDispatcher;
use Yiisoft\View\View;

use function basename;
use function str_replace;
use function sys_get_temp_dir;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    private ?ContainerInterface $container = null;

    protected function setUp(): void
    {
        FileHelper::ensureDirectory($this->getTestFilePath());
        $this->getContainer();
    }

    protected function tearDown(): void
    {
        $this->container = null;
        FileHelper::removeDirectory($this->getTestFilePath());
    }

    protected function get(string $id)
    {
        return $this
            ->getContainer()
            ->get($id);
    }

    protected function getTestFilePath(): string
    {
        return sys_get_temp_dir()
            . DIRECTORY_SEPARATOR
            . basename(str_replace('\\', '_', static::class))
            ;
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
            $tempDir = $this->getTestFilePath();
            $eventDispatcher = new SimpleEventDispatcher();
            $view = new View($tempDir, $eventDispatcher);
            $messageBodyTemplate = new MessageBodyTemplate($tempDir, '', '');
            $messageBodyRenderer = new MessageBodyRenderer($view, $messageBodyTemplate);
            $messageFactory = new MessageFactory(Message::class);
            $transport = new DummyTransport();

            $this->container = new SimpleContainer([
                EventDispatcherInterface::class => $eventDispatcher,
                MailerInterface::class => new Mailer($messageFactory, $messageBodyRenderer, $eventDispatcher, $transport),
                MessageBodyRenderer::class => new MessageBodyRenderer($view, $messageBodyTemplate),
                MessageBodyTemplate::class => $messageBodyTemplate,
                MessageFactoryInterface::class => $messageFactory,
                Swift_Transport::class => $transport,
            ]);
        }

        return $this->container;
    }
}
