<?php

abstract class Widget extends View {

    /**
     * @var array
     */
    protected $_data = array();

    /**
     * @var array
     */
    private static $_loaded = array();

    /**
     * @param string $id
     * @param string $namespace
     * @return mixed
     */
    public static function factory($id, $namespace){
        if(isset(self::$_loaded[$id])){
            $clazz = self::$_loaded[$id];
        } else {
            $info = Resource::getInfo($id);
            include_once self::$_template_dir . '/' . $info['uri'];
            $clazz = $info['extras']['clazz'];
            self::$_loaded[$id] = $clazz;
        }
        return new $clazz($id, $namespace);
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return $this|mixed
     */
    public function __call($method, $arguments){
        if(method_exists($this, $method)){
            return call_user_func_array(
                array( $this, $method ),
                $arguments
            );
        } else {
            if(empty($arguments)){
                $this->_data[$method] = true;
            } else {
                $this->_data[$method] = $arguments[0];
            }
            return $this;
        }
    }

    /**
     * @return string
     */
    protected abstract function tpl();

    /**
     * @return string
     */
    public function fetch(){
        ob_start();
        try {
            $this->tpl();
            $this->checkScope();
        } catch(Exception $e) {
            fis_error_reporter($e);
        }
        $content = ob_get_clean();
        $this->loadResource();
        return $content;
    }
}