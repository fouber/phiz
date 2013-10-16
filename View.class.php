<?php
/**
 * User: zhangyunlong
 * Date: 13-10-15
 * Time: 下午9:19
 */

abstract class View {

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

    /**
     * @param string $id
     * @param string $caller_namespace
     */
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
        $this->init();
    }

    /**
     * for subclasses
     */
    protected function init(){}

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function input($key, $default = null){
        if(isset($this->_data[$key])){
            return $this->_data[$key];
        } else {
            return $default;
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
     * 
     */
    protected function loadResource(){
        if($this->_deps){
            Resource::import($this->_id);
            unset($this->_deps);
        }
    }

    /**
     *
     */
    const CSS_PLACEHOLDER = '<!--[FIS_CSS_PLACEHOLDER]-->';

    /**
     *
     */
    const JS_PLACEHOLDER = '<!--[FIS_JS_PLACEHOLDER]-->';

    /**
     * @var bool
     */
    protected $_used_css_placeholder = false;

    /**
     * @var bool
     */
    protected $_used_js_placeholder = false;

    /**
     * @return string
     */
    public function fetch(){
        $content = $this->loadTemplate();
        $this->checkScope();
        $this->loadResource();
        if($this->_used_css_placeholder){
            $pos = strpos($content, self::CSS_PLACEHOLDER);
            if($pos !== false){
                $content = substr_replace($content, Resource::render('css'), $pos, strlen(self::CSS_PLACEHOLDER));
            }
        }
        if($this->_used_js_placeholder){
            $pos = strrpos($content, self::JS_PLACEHOLDER);
            if($pos !== false){
                $content = substr_replace($content, Resource::render('js'), $pos, strlen(self::JS_PLACEHOLDER));
            }
        }
        return $content;
    }

    /**
     * @return string
     */
    public function css(){
        $this->_used_css_placeholder = true;
        return self::CSS_PLACEHOLDER;
    }

    /**
     * @return string
     */
    public function js(){
        $this->_used_js_placeholder = true;
        return self::JS_PLACEHOLDER;
    }

    /**
     * @param string $name
     */
    public function startScript($name = 'normal'){
        Resource::startPool($name);
    }

    /**
     *
     */
    public function endScript(){
        Resource::endPool();
    }

    /**
     * @param string $name
     * @return string
     */
    public function script($name = 'normal'){
        return Resource::renderPool($name);
    }

    /**
     * 
     */
    public function display(){
        echo $this->fetch();
    }

    /**
     * @var array
     */
    private static $_loaded_widget = array();

    /**
     * @param string $__uri__
     * @return string
     */
    protected static function includeOnce($__uri__){
        if(self::$_template_dir){
            ob_start();
            include_once self::$_template_dir . '/' . $__uri__;
            return ob_get_clean();
        } else {
            trigger_error('undefined template dir', E_USER_ERROR);
        }
        return '';
    }

    /**
     * @param string $id
     * @return self
     */
    public function load($id){
        if(self::$_loaded_widget[$id]){
            $clazz = self::$_loaded_widget[$id];
            return new $clazz($id, $this->_namespace);
        } else {
            $info = Resource::getInfo($id);
            if(isset($info['extras']) && isset($info['extras']['clazz'])){
                $clazz = $info['extras']['clazz'];
                self::includeOnce($info['uri']);
                self::$_loaded_widget[$id] = $clazz;
                return new $clazz($id, $this->_namespace);
            } else {
                trigger_error('Undefined class name of widget [' . $id . ']', E_USER_ERROR);
            }
        }
        return null;
    }

    /**
     * @var Page
     */
    protected static $_page;

    /**
     * @param Page $page
     */
    public static function setPage(Page $page){
        self::$_page = $page;
    }

    /**
     * @return Page
     */
    public static function getPage(){
        return self::$_page;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed|null
     */
    public function getPageData($key, $default){
        if(self::$_page){
            return self::$_page->input($key, $default);
        } else {
            trigger_error('missing page instance');
        }
        return null;
    }

    /**
     * @return string
     */
    abstract protected function loadTemplate();

}