<?php
/**
 * User: zhangyunlong
 * Date: 13-10-15
 * Time: 下午9:19
 */

class View {

    /**
     * @var string
     */
    protected static $_template_dir;

    /**
     * @var array
     */
    protected $_data = array();

    /**
     * @var string
     */
    protected $_id;

    /**
     * @var string
     */
    protected $_namespace;

    /**
     * @var string
     */
    protected $_scope = 'protected';

    /**
     * @var string
     */
    protected $_uri;

    /**
     * @var array
     */
    protected $_deps;

    /**
     * @var array
     */
    protected $_res_info;

    /**
     * @var null|string
     */
    protected $_caller_namespace = null;
    
    public function __construct($id, $caller_namespace = null){
        $this->_id = $id;
        $this->_caller_namespace = $caller_namespace;
        $this->_info = $info = Resource::getInfo($id, $this->_namespace);
        $this->_uri = $info['uri'];
        if($this->_namespace === 'common'){
            $this->_scope = 'public';
        }
        if(isset($info['deps'])){
            $this->_deps = $info['deps'];
        }
    }

    /**
     * @var array
     */
    private static $_global_data = array();

    /**
     * @param string $id
     * @param string $key
     * @param mixed $value
     */
    public static function setGlobalData($id, $key, $value){
        self::$_global_data[$id][$key] = $value;
    }

    /**
     * @param string $id
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getGlobalData($id, $key, $default = null){
        if(isset(self::$_global_data[$id]) && isset(self::$_global_data[$id][$key])){
            return self::$_global_data[$id][$key];
        } else {
            return $default;
        }
    }

    protected function input($key, $default = null){
        if(isset($this->_data[$key])){
            return $this->_data[$key];
        } else {
            return self::getGlobalData($this->_id, $key, $default);
        }
    }

    /**
     * @param string $template_dir
     */
    public static function setTemplateDir($template_dir){
        self::$_template_dir = $template_dir;
    }

    /**
     * @return string
     */
    public static function getTemplateDir(){
        return self::$_template_dir;
    }

    /**
     * @param string $id
     * @param string &$ns
     * @return mixed
     */
    public function uri($id, &$ns = null){
        $info = Resource::getInfo($id, $ns);
        return $info['uri'];
    }

    /**
     * @param string $id
     * @param bool $async
     * @return string
     */
    public function import($id, $async = false){
        return Resource::import($id, $async);
    }

    /**
     * @param string $property
     * @param mixed $value
     * @return $this
     */
    public function assign($property, $value = null){
        if(is_string($property)){
            $this->_data[$property] = $value;
        } else if(is_array($property)){
            foreach($property as $k => $v){
                $this->assign(strval($k), $v);
            }
        } else {
            trigger_error('invalid assign data type', E_USER_ERROR);
        }
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $arguments
     * @return mixed
     */
    public function __call($name, $arguments){
        $value = count($arguments) === 0 ? true : $arguments[0];
        $this->assign($name, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function __toString(){
        return $this->fetch();
    }

    /**
     * @param string $type
     * @return bool
     */
    public function scope($type){
        $this->_scope = strtolower($type);
    }

    /**
     * @return bool
     */
    protected function checkScope(){
        if($this->_caller_namespace){
            switch($this->_scope){
                case 'private':
                    if($this->_namespace === $this->_caller_namespace){
                        return true;
                    }
                    break;
                case 'protected':
                    if(strpos($this->_caller_namespace . '-', $this->_namespace . '-') === 0){
                        return true;
                    }
                    break;
                case 'public':
                    return true;
                    break;
                default:
                    trigger_error('unsupport scope type [' . $this->_scope . ']', E_USER_ERROR);
            }
            trigger_error("unable to use [{$this->_scope}] resource [{$this->_id}]", E_USER_ERROR);
        }
        return false;
    }

    /**
     * @param string $uri
     * @return string
     */
    protected function loadTemplate($uri = null){
        $content = '';
        if(self::$_template_dir){
            ob_start();
            include self::$_template_dir . '/' . (isset($uri) ? $uri : $this->_uri);
            $content = ob_get_clean();
        } else {
            trigger_error('undefined template dir', E_USER_ERROR);
        }
        return $content;
    }

    /**
     * 
     */
    protected function loadResource(){
        if($this->_deps){
            Resource::import($this->_id);
            unset($this->_deps);
        }
    }

    /**
     * @return string
     */
    public function fetch(){
        $content = $this->loadTemplate();
        $this->checkScope();
        $this->loadResource();
        return $content;
    }

    /**
     * 
     */
    public function display(){
        echo $this->fetch();
    }

}