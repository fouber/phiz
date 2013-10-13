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
            $this->_parent = new self($id, $this->_namespace);
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
        $block = new self($id, $this->_namespace);
        $block->setContext($this->_context);
        return new Block($block->fetch());
    }
    
    public function widget($id, $context = array()){
        $widget = Widget::factory($id, $this->_namespace);
        $widget->setContext($context);
        return  $widget;
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
        $content = $this->loadTempalte($defs);
        if($this->_parent){
            if($content){
                $defs['body'] = new Block($content);
            }
            $this->_parent->assign($defs);
            $content = $this->_parent->fetch();
        }
        $this->loadResource();
        return $content;
    }
}