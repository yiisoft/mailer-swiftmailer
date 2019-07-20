<?php
namespace Yiisoft\Mailer\SwiftMailer\Tests;

use Psr\Log\{LoggerInterface, LogLevel};
use Yiisoft\Mailer\SwiftMailer\Logger;

class LoggerTest extends TestCase
{
    private function createLogger(): Logger
    {
        return new Logger($this->get(LoggerInterface::class));
    }

    /**
     * Data provider for [[testAdd()]]
     * @return array test data
     */
    public function dataProviderAdd(): array
    {
        return [
            [
                '>> command sent',
                [
                    'message' => '>> command sent',
                    'level' => LogLevel::INFO,
                ]
            ],
            [
                '<< response received',
                [
                    'message' => '<< response received',
                    'level' => LogLevel::INFO,
                ]
            ],
            [
                '++ transport started',
                [
                    'message' => '++ transport started',
                    'level' => LogLevel::DEBUG,
                ]
            ],
            [
                '!! error message',
                [
                    'message' => '!! error message',
                    'level' => LogLevel::WARNING,
                ]
            ],
            [
                '-- response received',
                [
                    'message' => '-- response received',
                    'level' => LogLevel::INFO,
                ]
            ],
        ];
    }

    /**
     * @dataProvider dataProviderAdd
     *
     * @param string $entry
     * @param array $expectedLogMessage
     */
    public function testAdd($entry, array $expectedLogMessage): void
    {
        /** @var \Yiisoft\Log\Logger $psrLogger */
        $psrLogger = $this->get(LoggerInterface::class);
        $logger = new Logger($psrLogger);

        $logger->add($entry);

        $logMessage = end($psrLogger->messages);

        $this->assertEquals($expectedLogMessage['level'], $logMessage[0]);
        $this->assertEquals($expectedLogMessage['message'], $logMessage[1]);
    }

    public function testClear(): void
    {
        $logger = $this->createLogger();
        $this->assertNull($logger->clear());
    }

    public function testDump(): void
    {
        $logger = $this->createLogger();
        $this->assertEquals('', $logger->dump());
    }
}
