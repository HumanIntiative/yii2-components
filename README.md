README
========
Yii2 Components for PKPU Dev Team

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist pkpudev/yii2-components "*"
```

or add

```
"pkpudev/yii2-components": "*"
```

to the require section of your `composer.json` file.

Checking Compatibility
------------

Check for PHP 5.6

```
./vendor/bin/phpcs --standard=PHPCompatibility --runtime-set testVersion 5.6 --extensions=php --report-full=report.txt --ignore=*/vendor/* .
```

Source [here](https://www.sitepoint.com/quick-intro-phpcompatibility-standard-for-phpcs-are-you-php7-ready/)

Installing PHPUnit
------------

Install phar version of phpunit locally

```
wget https://phar.phpunit.de/phpunit-<version>.phar
chmod +x phpunit-6.4.phar
mv phpunit-6.4.phar phpunit
./phpunit --version
```