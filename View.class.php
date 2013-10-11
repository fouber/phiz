<?php

function fis_error_reporter($msg){
    echo ( '<h3>[ERROR] ' . $msg . '</h3>');
    die();
}

class View {
    
    protected static $_template_dir;
    
    private static $_types = array('string','int','bool','float','array');
    
    protected $_scope = 'protected';
    protected $_namespace;
    protected $_id;
    protected $_uri;
    protected $_context = array();
    
    public function __construct($id){
        $this->_id = $id;
        $this->_uri = $this->uri($id, $this->_namespace);
    }
    
    public static function setTemplateDir($template_dir){
        self::$_template_dir = $template_dir;
    }
    
    public static function getTemplateDir(){
        return self::$_template_dir;
    }
    
    public function uri($id, &$ns = null, &$map = null){
        $info = Resource::getInfo($id, $ns, $map);
        return isset($info['uri']) ? $info['uri'] : null;
    }
    
    public function import($id, $async = false){
        return Resource::import($id, $async);
    }
    
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
    }
    
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
        
        return $input;
    }
    
    public function assign($property, $value = null){
        if(is_string($property)){
            $this->_context[$property] = $value;
        } else if(is_array($property)){
            $this->_context = array_merge($this->_context, $property);
        }
        return $this;
    }
    
    public function scope($type){
        $this->_scope = $type;
    }
    
    public function __toString(){
        return $this->fetch();
    }
    
    public function fetch(&$__defined_vars__ = null){
        if(self::$_template_dir){
            if($this->_uri){
                ob_start();
                extract($this->_context);
                include self::$_template_dir . '/' . $this->_uri;
                $__defined_vars__ = get_defined_vars();
                return ob_get_clean();
            } else {
                fis_error_reporter('unable to load template file [' . $this->_id . '] in [' . self::$_template_dir . ']');
            }
        } else {
            fis_error_reporter('undefined template dir');
        }
    }
    
    public function display(){
        echo $this->fetch();
    }
    
}