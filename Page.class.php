<?php
/**
 * User: zhangyunlong
 * Date: 13-10-16
 * Time: 下午4:21
 */

abstract class PhizPage extends PhizView {

    /**
     * @return string
     */
    protected function loadTemplate(){
        self::setPage($this);
        return $this->buildPage();
    }

    /**
     * @return string
     */
    abstract protected function buildPage();
    
}