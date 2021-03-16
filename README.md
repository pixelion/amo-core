Module banner
===========

Module for PIXELION CMS

[![Latest Stable Version](https://poser.pugx.org/panix/mod-banner/v/stable)](https://packagist.org/packages/panix/mod-banner)
[![Total Downloads](https://poser.pugx.org/panix/mod-banner/downloads)](https://packagist.org/packages/panix/mod-banner)
[![Monthly Downloads](https://poser.pugx.org/panix/mod-banner/d/monthly)](https://packagist.org/packages/panix/mod-banner)
[![Daily Downloads](https://poser.pugx.org/panix/mod-banner/d/daily)](https://packagist.org/packages/panix/mod-banner)
[![Latest Unstable Version](https://poser.pugx.org/panix/mod-banner/v/unstable)](https://packagist.org/packages/panix/mod-banner)
[![License](https://poser.pugx.org/panix/mod-banner/license)](https://packagist.org/packages/panix/mod-banner)


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer require --prefer-dist panix/mod-banner "*"
```

or add

```
"panix/mod-banner": "*"
```

to the require section of your `composer.json` file.

Add to web config.
```
'modules' => [
    'banner' => ['class' => 'panix\mod\banner\Module'],
]
```

#### Migrate
```
php yii migrate --migrationPath=vendor/panix/mod-banner/migrations
```
