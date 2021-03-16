AMO core
===========

PIXELION

[![Latest Stable Version](https://poser.pugx.org/pixelion/amo-core/v/stable)](https://packagist.org/packages/pixelion/amo-core)
[![Total Downloads](https://poser.pugx.org/pixelion/amo-core/downloads)](https://packagist.org/packages/pixelion/amo-core)
[![Monthly Downloads](https://poser.pugx.org/pixelion/amo-core/d/monthly)](https://packagist.org/packages/pixelion/amo-core)
[![Daily Downloads](https://poser.pugx.org/pixelion/amo-core/d/daily)](https://packagist.org/packages/pixelion/amo-core)
[![Latest Unstable Version](https://poser.pugx.org/pixelion/amo-core/v/unstable)](https://packagist.org/packages/pixelion/amo-core)
[![License](https://poser.pugx.org/pixelion/amo-core/license)](https://packagist.org/packages/pixelion/amo-core)


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer require --prefer-dist pixelion/amo-core "*"
```

or add

```
"pixelion/amo-core": "*"
```

to the require section of your `composer.json` file.

Add to web config.
```
'modules' => [
    'amocrm' => ['class' => 'Pixelion\AmoCrm\Module'],
]
```

#### Migrate
```
php yii migrate --migrationPath=vendor/pixelion/amo-core/migrations
```
