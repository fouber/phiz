<?php

class Resource {
    
    const MAP_EXT = '.json';
    
    private static $_map_dir;
    private static $_maps = array();
    private static $_collection = array();
    
    public static function init(){
        self::$_maps = array();
        self::$_collection = array();
    }
    
    public static function setMapDir($map_dir){
        self::$_map_dir = $map_dir;
    }
    
    public static function getMapDir(){
        return self::$_map_dir;
    }
    
    private static function getNamespace($id){
        $pos = strpos($id, ':');
        if($pos === false){
            return '__global__';
        } else {
            return substr($id, 0, $pos);
        }
    }
    
    public static function getInfo($id, &$ns = null, &$map = null){
        $ns = self::getNamespace($id);
        if(isset(self::$_maps[$ns])){
           $map = self::$_maps[$ns];
        } else {
            if(self::$_map_dir){
                if($ns === '__global__'){
                    $map_file = self::$_map_dir . '/map' . self::MAP_EXT;
                } else {
                    $map_file = self::$_map_dir . '/' . $ns . '-map' . self::MAP_EXT;
                }
                if(file_exists($map_file)){
                    if(self::MAP_EXT === '.php'){
                        $map = self::$_maps[$ns] = include $map_file;
                    } else {
                        $map = self::$_maps[$ns] = json_decode(file_get_contents($map_file), true);
                    }
                } else {
                    fis_error_reporter('unable to load reource map [' . $map_file . ']');
                }
            } else {
                fis_error_reporter('undefined resource map dir');
            }
        }
        return $map['res'][$id];
    }
}