<?php
/**
 * Description of tools
 * @author Damien.D & Stephane.G
 */

abstract class Tools {
    static public function now(){
        return date("Y-m-d H:i:s");
    }
    static public function capitalize($string){
        return strtoupper(substr($string,0,1)).substr($string,1);
    }
    static function getParamsURL($paramsURL){
        $params = array();
        if( !empty($paramsURL) ){
            $splitted_params = explode("&",$paramsURL);
            foreach($splitted_params as $param){
                $parts = explode("=", $param);
                $params[$parts[0]] = @$parts[1];
            }
        }
        return $params;
    }
}