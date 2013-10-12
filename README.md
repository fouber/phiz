PHP Template Engine
=======

## Demo

* [x-view-demo](https://github.com/fouber/x-view-demo)

## Quick Start

index.php

```php
require_once 'x-view/View.class.php';
require_once 'x-view/Resource.class.php';
require_once 'x-view/Block.class.php';
require_once 'x-view/Layout.class.php';

$dir = dirname(__FILE__);
View::setTemplateDir($dir . '/template');
Resource::setMapDir($dir . '/map');

$view = new Layout('page.php');
$view
  ->assign('title', 'foo')
  ->assign('word', 'hello world')
  ->display();
```

## View

* [require static](https://github.com/fouber/x-view-demo/blob/master/page.php#L3)
* [check input](https://github.com/fouber/x-view-demo/blob/master/layout.php#L5-L10)

## Layout extend View

* [extend](https://github.com/fouber/x-view-demo/blob/master/page.php#L5)
* [load block file](https://github.com/fouber/x-view-demo/blob/master/layout.php#L10)
* [render css](https://github.com/fouber/x-view-demo/blob/master/layout.php#L15)
* [render js](https://github.com/fouber/x-view-demo/blob/master/layout.php#L28)

## Block

* [fill](https://github.com/fouber/x-view-demo/blob/master/page.php#L15-L21)
* [append](https://github.com/fouber/x-view-demo/blob/master/page.php#L27-L29)
* [prepend](https://github.com/fouber/x-view-demo/blob/master/page.php#L23-L25)

## Dependencies

* [fis](https://github.com/fis-dev/fis)