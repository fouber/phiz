<?php

class Layout extends View {
    
    const CSS_PLACEHOLDER = '<!--[FIS_CSS_PLACEHOLDER]-->';
    const JS_PLACEHOLDER = '<!--[FIS_JS_PLACEHOLDER]-->';

    /**
     * @var Layout
     */
    private $_parent = null;

    /**
     * @param $id
     * @return $this
     */
    public function extend($id){
        if($this->_parent === null){
            $this->_parent = new self($id);
        } else {
            fis_error_reporter('unable to extend multiple layouts');
        }
        return $this;
    }

    /**
     * @param $id
     * @return Block|null
     */
    public function block($id){
        $block = new self($id);
        $block->assign($this->_context);
        return new Block($block->fetch());
    }

    /**
     * @return string
     */
    public function css(){
        return self::CSS_PLACEHOLDER;
    }

    /**
     * @return string
     */
    public function js(){
        return self::JS_PLACEHOLDER;
    }

    /**
     * 
     */
    public function display(){
        $content = $this->fetch();
        $pos = strpos($content, self::CSS_PLACEHOLDER);
        if($pos !== false){
            $content = substr_replace($content, Resource::render('css'), $pos, strlen(self::CSS_PLACEHOLDER));
        }
        $pos = strrpos($content, self::JS_PLACEHOLDER);
        if($pos !== false){
            $content = substr_replace($content, Resource::render('js'), $pos, strlen(self::JS_PLACEHOLDER));
        }
        echo $content;
    }

    /**
     * @return string
     */
    public function fetch(){
        $content = $this->loadTempalte($__defined_vars__);
        if($this->_parent){
            if($content){
                $__defined_vars__['body'] = new Block($content);
            }
            $this->_parent->assign($__defined_vars__);
            $content = $this->_parent->fetch();
        }
        $this->loadResource();
        return $content;
    }
}