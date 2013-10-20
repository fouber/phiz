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

source code (php style):

```php
<?php

    //access permission
    $this->scope('private');

    //static resources
    $this->import('lib/jquery/jquery.js');
    $this->import('lib/bootstrap/bootstrap.css');
    
    //inputs
    $content = $this->input('content', 'hello world');

?>
<!-- html of view -->
<div><?php echo $content; ?></div>
```

source code (php class style)

```php
<?php

class Foo_Widget_Bar extends PhizView
{

    protected function init()
    {
        //access permission
        $this->scope('private');
        
        //static resources
        $this->import('lib/jquery/jquery.js');
        $this->import('lib/bootstrap/bootstrap.css');
    }
    
    protected function loadTemplate()
    {
        //inputs
        $content = $this->input('content', 'hello world');
        $html  = '<!-- html of view -->';
        $html .= "<div>{$content}</div>";
        return $html;
    }
}
```

## API Documentation

### PhizView

* [input($key, $default = null)](https://github.com/fouber/phiz-demo/blob/master/common/layout/skeleton/skeleton.php#L12-L15): get inputs
* [import($id)](https://github.com/fouber/phiz-demo/blob/master/common/layout/skeleton/skeleton.php#L3-L6): require static resources.
* [load($id)](https://github.com/fouber/phiz-demo/blob/master/common/layout/skeleton/skeleton.php#L21): load other view.
* [scope($type)](https://github.com/fouber/phiz-demo/blob/master/foo/widget/table/table.php#L3): define access permission.
* [css()](https://github.com/fouber/phiz-demo/blob/master/common/layout/skeleton/skeleton.php#L24): display required css resources.
* [js()](https://github.com/fouber/phiz-demo/blob/master/common/layout/skeleton/skeleton.php#L29): display required js resources.
* [startScript()](https://github.com/fouber/phiz-demo/blob/master/foo-bar/widget/left/left.php#L12): ob_start to collect script code.
* [endScript()](https://github.com/fouber/phiz-demo/blob/master/foo-bar/widget/left/left.php#L12): ob_get_clean to stop collecting script code.
* [script()](https://github.com/fouber/phiz-demo/blob/master/common/layout/skeleton/skeleton.php#L30): display collected script code.
* [display()](https://github.com/fouber/phiz-demo/blob/master/foo-bar/index.php#L11): echo rendered html
* [fetch()](https://github.com/fouber/phiz-demo/blob/master/foo-bar/page/Index.class.php#L21): return rendered html
* [getPageData($key, $default)](https://github.com/fouber/phiz-demo/blob/master/common/layout/skeleton/skeleton.php#L12): get data from the unique PhizPage instance.

### PhizPage extend PhizView

* [buildPage()](https://github.com/fouber/phiz-demo/blob/master/foo-bar/page/Index.class.php#L7-L22): abstract, return page rendered html.

## Learn More

* [sdk](https://github.com/fouber/phiz-tool)
* [demo](https://github.com/fouber/phiz-demo)