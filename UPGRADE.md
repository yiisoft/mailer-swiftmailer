Upgrading Instructions for Yii Framework v2
===========================================

!!!IMPORTANT!!!

The following upgrading instructions are cumulative. That is,
if you want to upgrade from version A to version C and there is
version B between A and C, you need to following the instructions
for both A and B.

Upgrade from 'yii2-swiftmailer' 2.1.0
-------------------------------------

* Version constraint for "yiisoft/yii2" package has been raised to "~2.1.0". Make sure your code
  matches this version of the Yii framework.

* Configuration of the SwiftMailer transport changed to match the format used by "yiisoft/di".
  Use '__class' keyword for class specification and '__construct()' for constructor arguments.


Upgrade from 'yii2-swiftmailer' 2.0.7
-------------------------------------

* Minimal required version of the [swiftmailer/swiftmailer](https://github.com/swiftmailer/swiftmailer) library has been raised to 6.0.0.
  You should adjust your program according to the [change list](https://github.com/swiftmailer/swiftmailer/blob/v6.0.0/CHANGES#L4-L17) for this version.

* Since `Swift_MailTransport` has been removed in SwiftMailer 6.0.0, this extension now uses `Swift_SendmailTransport` by default.

* Minimum version of PHP has been raised to 7.0.
