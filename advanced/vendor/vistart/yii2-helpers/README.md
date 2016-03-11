Helpers of Yii 2 Extension
==========================
The helpers for Yii 2 application.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist vistart/yii2-helpers "*"
```

or add

```
"vistart/yii2-helpers": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
<?= \vistart\Helpers\Ip::IPv4toInteger('192.168.1.1'); ?>
```