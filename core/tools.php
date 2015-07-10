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
    /*OK*/static function getParams($request){
        $params = array();
        if( !empty($request) ){
            foreach($request as $key => $value){
				if($key != "300gpBAK" && $key != "300gp"){
					$params[$key] = $value;
				}
            }
        }
		//var_dump($params);
		//die;
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
    /*OK*/static function sendActivationMail(User $user, $idUser){
        $mail = $user->getEmail();
        $pseudo = $user->getPseudo();
        $link = "http://www.winds-game.com/page/login.php"
              . "?action=activate&id=".$idUser
                ."&token=".$user->getToken();
        $message_html = "<html><head></head><body><h1 style='margin-bottom:20px;'>Welcome to WINDS world, ".$pseudo." !</h1>";
        
        $message_html .= "<p>All you have to do is to click on this link to activate your account : <a href=\"";
        $message_html .= $link."\">Account activation</a>.</p>";
        $message_html .= "<p>You will be able to download the game in the \"Shop\" section.</p>";
        $message_html .= "<p>Please visit our forum too, to share with the Winds community !</p>";
        $message_html .= "<p>And again, welcome !</p>";
        $message_html .= "<p><em>The Winds Team</em></p>";
        $message_html .= "</body></html>";
        
        
        $passage_ligne = "\r\n";

        //=====Création de la boundary
        $boundary = "-----=".md5(rand());
        //==========

        //=====Définition du sujet.
        $subject = "Winds - Account activation";
        //=========

        //=====Création du header de l'e-mail.
        $header = "From: \"Winds team\"<team@winds-game.com>".$passage_ligne;
        $header.= "Reply-to: \"Winds team\" <team@winds-game.com>".$passage_ligne;
        $header.= "MIME-Version: 1.0".$passage_ligne;
        $header.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;
        //==========

        //=====Création du message.
        $message.= $passage_ligne."--".$boundary.$passage_ligne;
        //=====Ajout du message au format HTML
        $message.= "Content-Type: text/html; charset=\"ISO-8859-1\"".$passage_ligne;
        $message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
        $message.= $passage_ligne.$message_html.$passage_ligne;
        //==========
        $message.= $passage_ligne."--".$boundary."--".$passage_ligne;
        $message.= $passage_ligne."--".$boundary."--".$passage_ligne;
        //==========

        //=====Envoi de l'e-mail.
        return mail($mail,$subject,$message,$header);
        //==========
        
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
    
    static function displayResponse($data, $error=NULL){
        $response = array();
        if($data){  $response['data']  = $data;  }
        if($error){ $response['error'] = $error; }
        echo json_encode($response);
        die;
    }
    static function getThemesPath(){
        return $_SERVER['DOCUMENT_ROOT']."/addons/themes/";
    }
    static function getLevelsPath(){
        return $_SERVER['DOCUMENT_ROOT']."/addons/levels/";
    }
    static function getResourcesPath(){
        return $_SERVER['DOCUMENT_ROOT']."/resources/";
    }
    static function getEmptyLogoName(){
        return "logo-empty.png";
    }
}