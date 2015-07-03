<?php
/**
 * Description of tools
 * @author Damien.D & Stephane.G
 */

abstract class Tools {
    /*OK*/static public function today(){
        return date("Y-m-d");
    }
    /*OK*/static public function now(){
        return date("Y-m-d H:i:s");
    }
    /*OK*/static public function capitalize($string){
        return strtoupper(substr($string,0,1)).substr($string,1);
    }
    /*OK*/static function getIncomingParams(){
        $params = array();
        
        // collect parameters from URL request
        $paramsURL = $_SERVER['QUERY_STRING'];
        if( !empty($paramsURL) ){
            $splitted_params = explode("&",$paramsURL);
            foreach($splitted_params as $param){
                $parts = explode("=", $param);
                $params[$parts[0]] = @$parts[1];
            }
        }
        
        // collect parameters from file uploads
        if( !empty($_FILES) ){
            foreach($_POST as $key=>$val){
                $params[$key] = $val;
            }
            foreach($_FILES as $name=>$file){
                $params[$name] = $file;                
            }
        }

        return $params;
    }
    /*OK*/static function goToLogin(){
        header('location: login.php');
    }
    /*OK*/static function goToHome(){
        header('location: home.php');
    }
    /*OK*/static function generateRandomString(){
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charsLen = strlen($chars);
        $rndStr = '';
        for($i=0; $i<64; $i++) {
            $rndStr .= $chars[rand(0, $charsLen - 1)];
        }
        return $rndStr;
    }
    // to finish
    static function sendActivationMail(User $user){
        $link = "http://localhost/Winds/page/login.php"
              . "?action=activate&id=".$user->getId()
                ."&token=".$user->getToken();
        $subject = "Winds - Account activation";
        $message = $link;
        return mail($user->getEmail(), $subject, $message);
    }
    // to finish
    static function sendResetMail(User $user){
        $link = "http://localhost/Winds/page/login.php"
              . "?action=reset&id=".$user->getId()
                ."&token=".$user->getToken();
        $subject = "Winds - Password reset";
        $message = $link;
        return mail($user->getEmail(), $subject, $message);
    }
}