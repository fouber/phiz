<?php

class Block {
    
    private $_content = '';
    private static $_current_block = null;
    private static $_current_type = null;
    
    public function __construct($content = ''){
        $this->_content = $content;
    }
    
    public function __toString(){
        return $this->_content;
    }
    
    public function getContent(){
        return $this->_content;
    }
    
    public function setContent($content){
        $this->_content = $content;
    }
    
    public static function append(&$block){
        self::start($block, 'append');
    }
    
    public static function prepend(&$block){
        self::start($block, 'prepend');
    }
    
    public static function fill(&$block){
        self::start($block, 'fill');
    }
    
    private static function start(&$block, $type){
        if(self::$_current_block === null){
            if($block === null){
                $block = new self();
            } else if(!($block instanceOf self)){
                fis_error_reporter('invalid Block instance');
            }
            self::$_current_block = $block;
            self::$_current_type = $type;
            ob_start();
        } else {
            fis_error_reporter('invalid nested block');
        }
    }
    
    public static function end(){
        $content = ob_get_clean();
        $block = self::$_current_block;
        if($block){
            switch (self::$_current_type) {
                case 'prepend':
                    $content .= $block->getContent();
                    break;
                case 'append':
                    $content = $block->getContent() . $content;
                    break;
            }
            $block->setContent($content);
            self::$_current_block = null;
            self::$_current_type = null;
        } else {
            fis_error_reporter('missing block start');
        }
    }
}