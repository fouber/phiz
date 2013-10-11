<?php

class Block {

    /**
     * @var string
     */
    private $_content = '';

    /**
     * @var Block
     */
    private static $_current_block = null;

    /**
     * @var string
     */
    private static $_current_type = null;

    /**
     * @param string $content
     */
    public function __construct($content = ''){
        $this->_content = $content;
    }

    /**
     * @return string
     */
    public function __toString(){
        return $this->_content;
    }

    /**
     * @return string
     */
    public function getContent(){
        return $this->_content;
    }

    /**
     * @param $content
     */
    public function setContent($content){
        $this->_content = $content;
    }

    /**
     * @param Block|null $block
     */
    public static function append(&$block){
        self::start($block, 'append');
    }

    /**
     * @param Block|null $block
     */
    public static function prepend(&$block){
        self::start($block, 'prepend');
    }

    /**
     * @param Block|null $block
     */
    public static function fill(&$block){
        self::start($block, 'fill');
    }

    /**
     * @param Block|null $block
     * @param string $type
     */
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

    /**
     * @return Block|null
     */
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
        return $block;
    }
}