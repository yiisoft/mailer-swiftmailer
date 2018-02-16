<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\swiftmailer;

use Psr\Log\LogLevel;
use Yii;

/**
 * Logger is a SwiftMailer plugin, which allows passing of the SwiftMailer internal logs to the
 * Yii logging mechanism. Each native SwiftMailer log message will be converted into Yii 'info' log entry.
 *
 * This logger will be automatically created and applied to underlying [[\Swift_Mailer]] instance, if [[Mailer::$enableSwiftMailerLogging]]
 * is enabled. For example:
 *
 * ```php
 * [
 *     'components' => [
 *         'mailer' => [
 *             'class' => 'yii\swiftmailer\Mailer',
 *             'enableSwiftMailerLogging' => true,
 *         ],
 *      ],
 *     // ...
 * ],
 * ```
 *
 *
 * In order to catch logs written by this class, you need to setup a log route for 'yii\swiftmailer\Logger::add' category.
 * For example:
 *
 * ```php
 * [
 *     'components' => [
 *         'log' => [
 *             'targets' => [
 *                 [
 *                     'class' => 'yii\log\FileTarget',
 *                     'categories' => ['yii\swiftmailer\Logger::add'],
 *                 ],
 *             ],
 *         ],
 *         // ...
 *     ],
 *     // ...
 * ],
 * ```
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 2.0.4
 */
class Logger implements \Swift_Plugins_Logger
{
    /**
     * {@inheritdoc}
     */
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

        Yii::getLogger()->log($level, $entry, ['category' => __METHOD__]);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function dump()
    {
        return '';
    }
}