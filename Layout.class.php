<?php

class Layout extends View {

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

    public function css(){
        return Resource::render('css');
    }

    public function js(){
        return Resource::render('js');
    }

    /**
     * @return string
     */
    public function fetch(){
        $content = parent::fetch($__defined_vars__);
        if($this->_parent){
            if($content){
                $__defined_vars__['body'] = new Block($content);
            }
            $this->_parent->assign($__defined_vars__);
            $content = $this->_parent->fetch();
        }
        return $content;
    }
}