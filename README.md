<p align="center">
    <a href="https://swiftmailer.symfony.com/" target="_blank" rel="external">
        <img src="https://swiftmailer.symfony.com/images/logo.png" height="68px" style="background-color:#2a4fb7">
    </a>
    <h1 align="center">Yii Framework Swift Mailer Extension</h1>
    <br>
</p>

This library is a [Mailer](https://github.com/yiisoft/mailer) implementation that provides a [SwiftMailer](https://swiftmailer.symfony.com/) mail solution 
for [Yii framework](http://www.yiiframework.com).

For license information check the [LICENSE](LICENSE.md)-file.

[![Latest Stable Version](https://poser.pugx.org/yiisoft/mailer-swiftmailer/v/stable.png)](https://packagist.org/packages/yiisoft/mailer-swiftmailer)
[![Total Downloads](https://poser.pugx.org/yiisoft/mailer-swiftmailer/downloads.png)](https://packagist.org/packages/yiisoft/mailer-swiftmailer)
[![Build status](https://github.com/yiisoft/mailer-swiftmailer/workflows/build/badge.svg)](https://github.com/yiisoft/mailer-swiftmailer/actions?query=workflow%3Abuild)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/mailer-swiftmailer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/mailer-swiftmailer/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/yiisoft/mailer-swiftmailer/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/mailer-swiftmailer/?branch=master)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyiisoft%2Fmailer-swiftmailer%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/mailer-swiftmailer/master)
[![static analysis](https://github.com/yiisoft/mailer-swiftmailer/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/mailer-swiftmailer/actions?query=workflow%3A%22static+analysis%22)
[![type-coverage](https://shepherd.dev/github/yiisoft/mailer-swiftmailer/coverage.svg)](https://shepherd.dev/github/yiisoft/mailer-swiftmailer)


## Installation

The preferred way to install this library is through [composer](http://getcomposer.org/download/).

```
php composer.phar require --prefer-dist yiisoft/mailer-swiftmailer
```

## Usage

```php
$mailer->compose('contact/html')
    ->setFrom('from@domain.com')
    ->setTo('to@domain.com')
    ->setSubject($subject)
    ->send();
```

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
