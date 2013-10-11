<?php

class Layout extends View {
    
    private $_parent = null;
    
    public function extend($id){
        if($this->_parent === null){
            $this->_parent = new self($id);
        } else {
            fis_error_reporter('unable to extend multiple layouts');
        }
    }
    
    public function block($id){
        if(self::$_template_dir){
            $__uri__ = $this->uri($id);
            if($__uri__){
                ob_start();
                extract($this->_context);
                include self::$_template_dir . '/' . $__uri__;
                return new Block(ob_get_clean());
            } else {
                fis_error_reporter('unable to load block file [' . $this->_id . '] in [' . self::$_template_dir . ']');
            }
        } else {
            fis_error_reporter('undefined template dir');
        }
    }
    
    public function fetch(&$__defined_vars__ = null){
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