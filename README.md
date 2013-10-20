Pure PHP Template Engine
=======

## Demo

* [phiz-demo](https://github.com/fouber/phiz-demo)

## Quick Start

index.php

```php
<?php
require_once 'phiz/View.class.php';
$dir = dirname(__FILE__);
PhizView::setTemplateDir($dir . '/template');
PhizView::setMapDir($dir . '/map');
//init page and display
PhizView::page('foo-bar:page/Index.class.php')->display();
```

## PhizView

> a widget or block

source (php style):

```php
<?php

    //static resources
    $this->import('lib/jquery/jquery.js');
    $this->import('lib/bootstrap/bootstrap.css');

    //access permission
    $this->scope('private');
    
    //inputs
    $content = $this->input('content', 'hello world');

?>
<!-- html of view -->
<div><?php echo $content; ?></div>
```

## Page extend View

## Learn More

* [sdk](https://github.com/fouber/phiz-tool)
* [demo](https://github.com/fouber/phiz-demo)