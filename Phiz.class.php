<?php
/**
 * User: zhangyunlong
 * Date: 13-10-18
 * Time: 上午1:58
 */

$root = dirname(__FILE__);
require_once $root . '/View.class.php';
require_once $root . '/Page.class.php';
require_once $root . '/Resource.class.php';

class Phiz {

    /**
     * @param $id
     * @return self|null
     */
    public static function page($id){
        return PhizView::factory($id);
    }

    /**
     * @param string $template_dir
     */
    public static function setTemplateDir($template_dir){
        PhizView::setTemplateDir($template_dir);
    }

    /**
     * @param string $map_dir
     */
    public static function setMapDir($map_dir){
        PhizResource::setMapDir($map_dir);
    }
}