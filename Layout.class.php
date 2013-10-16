<?php
/**
 * User: zhangyunlong
 * Date: 13-10-15
 * Time: 下午9:20
 */

class Layout extends View {

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
    protected $_has_css_placeholder = false;

    /**
     * @var bool
     */
    protected $_has_js_placeholder = false;

    /**
     * @var array
     */
    private static $_loaded_widget = array();

    /**
     * @param string $id
     * @return View
     */
    public function load($id){
        if(self::$_loaded_widget[$id]){
            $clazz = self::$_loaded_widget[$id];
            return new $clazz($id, $this->_namespace);
        } else {
            $info = Resource::getInfo($id);
            if(isset($info['extras']) && isset($info['extras']['clazz'])){
                $clazz = $info['extras']['clazz'];
                $this->loadTemplate($info['uri']);
                self::$_loaded_widget[$id] = $clazz;
                return new $clazz($id, $this->_namespace);
            } else {
                return new self($id, $this->_namespace);
            }
        }
    }

    /**
     * @return string
     */
    public function fetch(){
        $content = parent::fetch();
        if($this->_has_css_placeholder){
            $pos = strpos($content, self::CSS_PLACEHOLDER);
            if($pos !== false){
                $content = substr_replace($content, Resource::render('css'), $pos, strlen(self::CSS_PLACEHOLDER));
            }
        }
        if($this->_has_js_placeholder){
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
        $this->_has_css_placeholder = true;
        return self::CSS_PLACEHOLDER;
    }

    /**
     * @return string
     */
    public function js(){
        $this->_has_js_placeholder = true;
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
    
}