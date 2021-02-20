<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://yiisoft.github.io/docs/images/yii_logo.svg" height="68px">
    </a>
    <a href="https://swiftmailer.symfony.com/" target="_blank" rel="external">
        <img src="https://swiftmailer.symfony.com/images/logo.png" height="68px">
    </a>
    <h1 align="center">Yii Mailer Library - Swift Mailer Extension</h1>
    <br>
</p>

[![Latest Stable Version](https://poser.pugx.org/yiisoft/mailer-swiftmailer/v/stable.png)](https://packagist.org/packages/yiisoft/mailer-swiftmailer)
[![Total Downloads](https://poser.pugx.org/yiisoft/mailer-swiftmailer/downloads.png)](https://packagist.org/packages/yiisoft/mailer-swiftmailer)
[![Build status](https://github.com/yiisoft/mailer-swiftmailer/workflows/build/badge.svg)](https://github.com/yiisoft/mailer-swiftmailer/actions?query=workflow%3Abuild)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/mailer-swiftmailer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/mailer-swiftmailer/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/yiisoft/mailer-swiftmailer/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/mailer-swiftmailer/?branch=master)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyiisoft%2Fmailer-swiftmailer%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/mailer-swiftmailer/master)
[![static analysis](https://github.com/yiisoft/mailer-swiftmailer/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/mailer-swiftmailer/actions?query=workflow%3A%22static+analysis%22)
[![type-coverage](https://shepherd.dev/github/yiisoft/mailer-swiftmailer/coverage.svg)](https://shepherd.dev/github/yiisoft/mailer-swiftmailer)

This package is a [yiisoft/mailer](https://github.com/yiisoft/mailer) library implementation that provides
a [Swift Mailer](https://swiftmailer.symfony.com/) mail solution.

## Installation

The package could be installed with composer:

```
composer require yiisoft/mailer-swiftmailer
```

## General usage

Creating a mailer:

```php
use Yiisoft\Mailer\MessageBodyRenderer;
use Yiisoft\Mailer\MessageBodyTemplate;
use Yiisoft\Mailer\MessageFactory;
use Yiisoft\Mailer\SwiftMailer\Mailer;
use Yiisoft\Mailer\SwiftMailer\Message;

/**
 * @var \Psr\EventDispatcher\EventDispatcherInterface $dispatcher
 * @var \Swift_Events_EventListener[] $plugins
 * @var \Swift_Transport $transport
 * @var \Yiisoft\View\View $view
 */

$template = new MessageBodyTemplate('/path/to/directory/of/view-files');

$mailer = new Mailer(
    new MessageFactory(Message::class),
    new MessageBodyRenderer($view, $template),
    $dispatcher,
    $transport,
    $plugins, // By default, an empty array
);
```

Sending a mail message:

```php
$message = $mailer->compose()
    ->withFrom('from@domain.com')
    ->withTo('to@domain.com')
    ->withSubject('Message subject')
    ->withTextBody('Plain text content')
    ->withHtmlBody('<b>HTML content</b>')
;
$mailer->send($message);
// Or several
$mailer->sendMultiple([$message]);
```

Additional methods of the `Yiisoft\Mailer\SwiftMailer\Message`:

- `getSwiftMessage()` - Returns a Swift message instance.
- `getReturnPath()` - Returns the return-path (the bounce address) of this message.
- `withReturnPath()` - Returns a new instance with the specified return-path (the bounce address) of this message.
- `getPriority()` - Returns the priority of this message.
- `withPriority()` - Returns a new instance with the specified priority of this message.
- `getReadReceiptTo()` - Returns the addresses to which a read-receipt will be sent.
- `withReadReceiptTo()` - Returns a new instance with the specified ask for a delivery receipt from the recipient to be sent to address.
- `withAttachedSigners()` - Returns a new instance with the specified attached signers.

For use in the [Yii framework](http://www.yiiframework.com/), see the configuration files:

- [`config/common.php`](https://github.com/yiisoft/mailer-swiftmailer/blob/master/config/common.php)
- [`config/params.php`](https://github.com/yiisoft/mailer-swiftmailer/blob/master/config/params.php)

See [Yii guide to mailing](https://github.com/yiisoft/docs/blob/master/guide/en/runtime/mailing.md) for more info.

### Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
./vendor/bin/phpunit
```

### Mutation testing

The package tests are checked with [Infection](https://infection.github.io/) mutation framework. To run it:

```shell
./vendor/bin/infection
```

### Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```shell
./vendor/bin/psalm
```

### Support the project

[![Open Collective](https://img.shields.io/badge/Open%20Collective-sponsor-7eadf1?logo=open%20collective&logoColor=7eadf1&labelColor=555555)](https://opencollective.com/yiisoft)

### Follow updates

[![Official website](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)
[![Twitter](https://img.shields.io/badge/twitter-follow-1DA1F2?logo=twitter&logoColor=1DA1F2&labelColor=555555?style=flat)](https://twitter.com/yiiframework)
[![Telegram](https://img.shields.io/badge/telegram-join-1DA1F2?style=flat&logo=telegram)](https://t.me/yii3en)
[![Facebook](https://img.shields.io/badge/facebook-join-1DA1F2?style=flat&logo=facebook&logoColor=ffffff)](https://www.facebook.com/groups/yiitalk)
[![Slack](https://img.shields.io/badge/slack-join-1DA1F2?style=flat&logo=slack)](https://yiiframework.com/go/slack)

## License

The Yii Framework Swift Mailer Extension is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE.md) for more information.

Maintained by [Yii Software](https://www.yiiframework.com/).
