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
[![Build Status](https://travis-ci.org/yiisoft/mailer-swiftmailer.svg?branch=master)](https://travis-ci.org/yiisoft/mailer-swiftmailer)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/mailer-swiftmailer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/mailer-swiftmailer/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/yiisoft/mailer-swiftmailer/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/mailer-swiftmailer/?branch=master)

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
