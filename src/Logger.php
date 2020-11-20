<?php

declare(strict_types=1);

namespace Yiisoft\Mailer\SwiftMailer;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Logger is a PSR-3 logger plugin for SwiftMailer.
 */
class Logger implements \Swift_Plugins_Logger
{
    /**
     * @var LoggerInterface logger instance.
     */
    private $psrLogger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $psrLogger)
    {
        $this->psrLogger = $psrLogger;
    }

    public function add($entry)
    {
        $categoryPrefix = substr($entry, 0, 2);
        switch ($categoryPrefix) {
            case '++':
                $level = LogLevel::DEBUG;
                break;
            case '>>':
            case '<<':
                $level = LogLevel::INFO;
                break;
            case '!!':
                $level = LogLevel::WARNING;
                break;
            default:
                $level = LogLevel::INFO;
        }

        $this->psrLogger->log($level, $entry, ['category' => __METHOD__]);
    }

    public function clear()
    {
        // do nothing
    }

    public function dump()
    {
        return '';
    }
}
