<?php

function fis_error_reporter($msg){
    echo ob_get_clean();
    echo ( '<h3>[ERROR] ' . $msg . '</h3>');
    die();
}

class View {

    /**
     * @var string
     */
    protected static $_template_dir;

    /**
     * @var array
     */
    private static $_types = array('string','int','bool','float','array');

    /**
     * @var string
     */
    protected $_scope = 'protected';

    /**
     * @var string
     */
    protected $_namespace;

    /**
     * @var null|string
     */
    protected $_caller_namespace = null;

    /**
     * @var string
     */
    protected $_id;

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
    protected $_info;

    /**
     * @var array
     */
    protected $_context = array();

    /**
     * @param string $id
     * @param string|null $caller_namespace
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
     * @param mixed $value
     * @param string $type
     * @return bool
     */
    protected static function typeCheck($value, $type) {
        if(in_array($type, self::$_types)) {
            if($type === 'array') {
                if(!(is_array($value) || $value instanceof ArrayAccess)) {
                    return false;
                } else {
                    return true;
                }
            } else {
                $fn = 'is_' . $type;
                if(!$fn($value)) {
                    return false;
                } else {
                    return true;
                }
            }
        } else {
            if(!$value instanceof $type) {
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * @param &$stack
     * @return mixed
     */
    protected function getInputVarName(&$stack){
        $stacks = debug_backtrace();
        $stack = $stacks[1];
        if(isset($stack)){
            $script = explode("\n", file_get_contents($stack['file']));
            $lineNumber = $stack['line'] - 1;
            $line = $script[$lineNumber];
            preg_match('/\$this\s*->\s*input\s*\(\s*(\$[a-zA-Z_][a-zA-Z_0-9]*)/', $line, $matches);
            return $matches[1];
        }
        return null;
    }

    /**
     * @param mixed &$input
     * @param string $type
     * @param mixed $default
     * @return $this
     */
    public function input(&$input, $type, $default = null){
        if($input === NULL && $default !== null){
            $input = $default;
        } else {
            if($input === null) {
                $var_name = $this->getInputVarName($stack);
                if($var_name) {
                    fis_error_reporter("Missing input '{$var_name}' in file {$stack['file']}, line {$stack['line']}");
                }
                fis_error_reporter('Missing required ' . $type . ' input in [' . $this->_id . ']');
            }
        }
        
        if(!self::typeCheck($input, $type)) {
            $passed = gettype($input);
            if($passed === 'object') {
                $passed = get_class($input);
            }
            $var_name = $this->getInputVarName($stack);
            if($var_name) {
                fis_error_reporter("Input '{$var_name}' type mismatch, expected: '{$type}', actual: '{$passed}' in file {$stack['file']}, line {$stack['line']}");
            }
            fis_error_reporter("Input type mismatch, expected: '$type', actual:'$passed'.");
        }
        return $this;
    }

    /**
     * @return array
     */
    protected function getContext(){
        return $this->_context;
    }

    /**
     * @param array $context
     */
    protected function setContext($context){
        if(is_array($context)){
            $this->_context = $context;
        } else {
            fis_error_reporter('invalid context data');
        }
    }

    /**
     * @param string $property
     * @param mixed $value
     * @return $this
     */
    public function assign($property, $value = null){
        if(is_string($property)){
            $this->_context[$property] = $value;
        } else if(is_array($property)){
            foreach($property as $k => $v){
                $this->assign($k, $v);
            }
        } else {
            fis_error_reporter('invalid assign data');
        }
        return $this;
    }

    /**
     * @param string $type
     * @return bool
     */
    public function scope($type){
        $this->_scope = strtolower($type);
    }
    
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
                    fis_error_reporter('unsupport scope type [' . $this->_scope . ']');
            }
            fis_error_reporter("unable to use [{$this->_scope}] resource [{$this->_id}]");
        }
        return false;
    }

    /**
     * @return string
     */
    public function __toString(){
        return $this->fetch();
    }

    /**
     * @param mixed &$__defined_vars__
     * @return string
     */
    protected function loadTempalte(&$__defined_vars__ = null){
        if(self::$_template_dir){
            if($this->_uri){
                ob_start();
                try {
                    extract($this->_context);
                    include self::$_template_dir . '/' . $this->_uri;
                    $__defined_vars__ = get_defined_vars();
                } catch(Exception $e) {
                    fis_error_reporter($e);
                }
                $this->checkScope();
                return ob_get_clean();
            } else {
                fis_error_reporter('unable to load template file [' . $this->_id . '] in [' . self::$_template_dir . ']');
            }
        } else {
            fis_error_reporter('undefined template dir');
        }
        return '';
    }
    
    protected function loadResource(){
        if($this->_deps){
            Resource::import($this->_id);
            unset($this->_deps);
        }
    }

    /**
     * @param &$defined_vars
     * @return string
     */
    public function fetch(&$defined_vars = null){
        $content = $this->loadTempalte($defined_vars);
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