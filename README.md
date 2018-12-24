<p align="center">
    <a href="https://swiftmailer.symfony.com/" target="_blank" rel="external">
        <img src="https://swiftmailer.symfony.com/images/logo.png" height="68px" style="background-color:#2a4fb7">
    </a>
    <h1 align="center">Yii Framework Swift Mailer Extension</h1>
    <br>
</p>

This extension provides a [SwiftMailer](https://swiftmailer.symfony.com/) mail solution for [Yii framework](http://www.yiiframework.com).

For license information check the [LICENSE](LICENSE.md)-file.

[![Latest Stable Version](https://poser.pugx.org/yiisoft/yii-swiftmailer/v/stable.png)](https://packagist.org/packages/yiisoft/yii-swiftmailer)
[![Total Downloads](https://poser.pugx.org/yiisoft/yii-swiftmailer/downloads.png)](https://packagist.org/packages/yiisoft/yii-swiftmailer)
[![Build Status](https://travis-ci.org/yiisoft/yii-swiftmailer.svg?branch=master)](https://travis-ci.org/yiisoft/yii-swiftmailer)

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

```
php composer.phar require --prefer-dist yiisoft/yii-swiftmailer
```

## Usage

To use this extension,  simply add the following code in your application configuration:

```php
return [
    //....
    'mailer' => [
        '__class' => yii\swiftmailer\Mailer::class,
    ],
];
```

You can then send an email as follows:

```php
$app->get('mailer')->compose('contact/html')
     ->setFrom('from@domain.com')
     ->setTo($form->email)
     ->setSubject($form->subject)
     ->send();
```

For further instructions refer to the [related section in the Yii Definitive Guide](http://www.yiiframework.com/doc-2.0/guide-tutorial-mailing.html).

