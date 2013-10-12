PHP Template Engine
=======

## init

set template dir & resource map dir.

```php
View::setTemplateDir('path/to/template/dir');
Resource::setMapDir('path/to/resource/map/dir');
```

## Quick Start

controller.php

```php
$view = new Layout('index.php');
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