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

## Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```php
./vendor/bin/phpunit
```

## Mutation testing

The package tests are checked with [Infection](https://infection.github.io/) mutation framework. To run it:

```php
./vendor/bin/infection
```

## Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/docs/). To run static analysis:

```php
./vendor/bin/psalm
```
