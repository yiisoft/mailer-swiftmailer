# Upgrading Instructions for Yii Framework Swift Mailer Extension 

!!!IMPORTANT!!!

The following upgrading instructions are cumulative. That is,
if you want to upgrade from version A to version C and there is
version B between A and C, you need to following the instructions
for both A and B.

## Upgrade from yii2-swiftmailer


* Configuration of the SwiftMailer transport changed to match the format used by "yiisoft/di".
  Use '__class' keyword for class specification and '__construct()' for constructor arguments.
