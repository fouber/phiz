<?php
/**
 * User: zhangyunlong
 * Date: 13-10-16
 * Time: 下午4:21
 */

abstract class Page extends View {

    /**
     * @return string
     */
    protected function loadTemplate(){
        self::setPage($this);
        return $this->buildPage();
    }

    /**
     * @param $id
     * @return self|null
     */
    public static function create($id){
        $info = Resource::getInfo($id);
        if(isset($info['extras']) && isset($info['extras']['clazz'])){
            self::includeOnce($info['uri']);
            $clazz = $info['extras']['clazz'];
            return new $clazz($id);
        }
        trigger_error("undefined class name of page [{$id}]");
        return null;
    }

    /**
     * @return string
     */
    abstract protected function buildPage();
    
}