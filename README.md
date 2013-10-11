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

## Layout

index.php

```php
<?php
    $this
        ->extend('layout.php')
        ->input($word, 'string');
    $title = 'page title';
?>
<h1><?php echo $word; ?></h1>
```

layout.php

```php
<?php
    $this
      ->extend('layout.php')
      ->input($title, 'string', 'untitled')
      ->input($body, 'Block');
?>
<!doctype html>
<html>
<head>
    <title><?php echo $title; ?></title>
</head>
<body>
    <?php echo $body; ?>
</body>
</html>
```