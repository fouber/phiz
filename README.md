Pure PHP Template Engine
=======

## Demo

* ``TODO``

## Quick Start

index.php

```php
require_once 'phiz/Phiz.class.php';

$dir = dirname(__FILE__);
Phiz::setTemplateDir($dir . '/template');
Phiz::setMapDir($dir . '/map');
Phiz::page('foo:page/Index.class.php')->user('fouber')->display();
```

## View

## Page extend View

* [phiz](https://github.com/fouber/phiz)