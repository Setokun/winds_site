<?php
/**
 * Description of tools file
 * @author Damien.D & Stephane.G
 */

/**
 * Class which contains utility methods.
 */
/*OK*/abstract class Tools {
    
    /**
     * Get the current date in "Y-m-d" format.
     * @return string
     */
    static public function today(){
        return date("Y-m-d");
    }
    /**
     * Get the current datetime in "Y-m-d H:i:s" format.
     * @return string
     */
    static public function now(){
        return date("Y-m-d H:i:s");
    }
    /**
     * Get the upper case string from the specified string.
     * @param string $string The string to get in upper case
     * @return string
     */
    static public function capitalize($string){
        return strtoupper(substr($string,0,1)).substr($string,1);
    }
    /**
     * Get an associative array of parameters sended to the server.
     * @return array
     */
    static function getIncomingParams(){
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
    /**
     * Returns the cleaned parameters from the specified array request.
     * @param array $request The array-formatted request coming from the game
     * @return array
     */
    static function getParams($request){
        $params = array();
        if( !empty($request) ){
            foreach($request as $key => $value){
                if($key != "300gpBAK" && $key != "300gp"){
                        $params[$key] = $value;
                }
            }
        }
        return $params;
    }
    /**
     * Redirect the browser to the login page.
     */
    static function goToLogin(){
        header('location: login.php');
    }
    /**
     * Redirect the browser to the home page of the current logged user.
     */
    static function goToHome(){
        header('location: home.php');
    }
    /**
     * Generates a random string which contains 64 alphanumeric characters.
     */
    static function generateRandomString(){
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charsLen = strlen($chars);
        $rndStr = '';
        for($i=0; $i<64; $i++) {
            $rndStr .= $chars[rand(0, $charsLen - 1)];
        }
        return $rndStr;
    }
    
    // to finish
    static function sendActivationMail(User $user, $idUser){
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
    // to test
    static function sendResetMail(User $user){
        $mail = $user->getEmail();
        $pseudo = $user->getPseudo();
        $link = "http://www.winds-game.com/page/login.php"
              . "?action=reset&id=".$user->getId()
                ."&token=".$user->getToken();
        
        $message_html = "<html><head></head><body><h1 style='margin-bottom:20px;'>Password reset</h1>";
        
        $message_html .= "<p>All you have to do is to click on this link to reset your password : <a href=\"";
        $message_html .= $link."\">Password reset</a>.</p>";
        $message_html .= "<br><p>Thank you !</p>";
        $message_html .= "</body></html>";
        
        
        $passage_ligne = "\r\n";

        //=====Création de la boundary
        $boundary = "-----=".md5(rand());
        //==========

        //=====Définition du sujet.
        $subject = "Winds - Password reset";
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
    // to test
    static function sendInscriptionConfirmationMail(User $user){
        $mail = $user->getEmail();
        $pseudo = $user->getPseudo();
        $link = "http://www.winds-game.com";
              
        $message_html = "<html><head></head><body><h1 style='margin-bottom:20px;'>Welcome to WINDS world, ".$pseudo." !</h1>";
        
        $message_html .= "<h2>You account has been successfully activated !</h2>";
        $message_html .= "<p>Please follow this <a href='".$link."'>link</a> to connect to your account.</p>";
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
    //to test
    static function sendPromotionMail(User $user, $newStatus){
        $mail = $user->getEmail();
        $pseudo = $user->getPseudo();
        $link = "http://www.winds-game.com";
              
        $message_html = "<html><head></head><body><h1 style='margin-bottom:20px;'>You've been granted to \"".$newStatus."\", ".$pseudo." !</h1>";
        
        $message_html .= "<h2>You got a promotion !</h2>";
        $message_html .= "<p>Now you can moderate levels and/or people, as you can see on the extended menu.</p>";
        $message_html .= "<p>Thank you for your involvement in <a href='".$link."'>www.winds-game.com</a>.</p>";
        $message_html .= "<p>Please visit our forum too, to share with the Winds community !</p>";
        $message_html .= "<br><p><em>The Winds Team</em></p>";
        $message_html .= "</body></html>";
        
        
        $passage_ligne = "\r\n";

        //=====Création de la boundary
        $boundary = "-----=".md5(rand());
        //==========

        //=====Définition du sujet.
        $subject = "Winds - Promotion";
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
    //to test
    static function sendRegressionMail(User $user){
        $mail = $user->getEmail();
        $pseudo = $user->getPseudo();
        $link = "http://www.winds-game.com";
              
        $message_html = "<html><head></head><body><h1 style='margin-bottom:20px;'>You're a simpler player now, ".$pseudo." !</h1>";
        
        $message_html .= "<h2>Back to the players !</h2>";
        $message_html .= "<p>Thank you for your involvement in <a href='".$link."'>www.winds-game.com</a>, but we decided you'll be just a player from now.</p>";
        $message_html .= "<br><p><em>The Winds Team</em></p>";
        $message_html .= "</body></html>";
        
        
        $passage_ligne = "\r\n";

        //=====Création de la boundary
        $boundary = "-----=".md5(rand());
        //==========

        //=====Définition du sujet.
        $subject = "Winds - Important information about your Winds account";
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
    //to test
    static function sendBanishMail(User $user){
        $mail = $user->getEmail();
        $pseudo = $user->getPseudo();
        $link = "http://www.winds-game.com";
        
        $message_html = "<html><head></head><body><h1 style='margin-bottom:20px;'>You've been banished, ".$pseudo." !</h1>";
        
        $message_html .= "<p>Thank you for being part if Winds community, but we decided you won't be able to share with the rest of the comunnity, because you acted like a jerk on the forum.</p>";
        $message_html .= "<br><p><em>The Winds Team</em></p>";
        $message_html .= "</body></html>";
        
        
        $passage_ligne = "\r\n";

        //=====Création de la boundary
        $boundary = "-----=".md5(rand());
        //==========

        //=====Définition du sujet.
        $subject = "Winds - Bashishment";
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
    // to test
    static function sendLevelAcceptedMail(User $user, Level $level){
        $mail = $user->getEmail();
        $pseudo = $user->getPseudo();
        $link = "http://www.winds-game.com";
        
        $message_html = "<html><head></head><body><h1 style='margin-bottom:20px;'>One of your custom levels has been accepted, ".$pseudo." !</h1>";
        
        $message_html .= "<h2>\"".$level->getName()."\" has been accepted</h2>";
        $message_html .= "<p>Now you can download the custom level you submited, like everybody in the community !</p>";
        $message_html .= "<p>Thank you for your involvement in <a href='".$link."'>www.winds-game.com</a>.</p>";
        $message_html .= "<br><p><em>The Winds Team</em></p>";
        $message_html .= "</body></html>";
        
        
        $passage_ligne = "\r\n";

        //=====Création de la boundary
        $boundary = "-----=".md5(rand());
        //==========

        //=====Définition du sujet.
        $subject = "Winds - Custom level accepted !";
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
    // to test
    static function sendLevelDeclinedMail(User $user, Level $level){
        $mail = $user->getEmail();
        $pseudo = $user->getPseudo();
        $link = "http://www.winds-game.com";
        
        $message_html = "<html><head></head><body><h1 style='margin-bottom:20px;'>One of your custom levels has been declined, ".$pseudo." !</h1>";
        
        $message_html .= "<h2>\"".$level->getName()."\" has been declined</h2>";
        $message_html .= "<p>It seems the level you submited was not good enough to be published, but don't give up hope, and try again !</p>";
        $message_html .= "<p>Thank you for your involvement in <a href='".$link."'>www.winds-game.com</a>.</p>";
        $message_html .= "<br><p><em>The Winds Team</em></p>";
        $message_html .= "</body></html>";
        
        
        $passage_ligne = "\r\n";

        //=====Création de la boundary
        $boundary = "-----=".md5(rand());
        //==========

        //=====Définition du sujet.
        $subject = "Winds - Custom level declined";
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
    
    /**
     * Display the JSON-formatted ajax response from the specified parameters.
     * @param string $data Data to be displayed if the ajax operation succeed
     * @param string $error Data to be displayed if the ajax operation fail
     */
    static function displayResponse($data, $error=NULL){
        $response = array();
        if($data){  $response['data']  = $data;  }
        if($error){ $response['error'] = $error; }
        echo json_encode($response);
        die;
    }
    /**
     * Get the themes folder path into the server.
     * @return string
     */
    static function getThemesPath(){
        return $_SERVER['DOCUMENT_ROOT']."/addons/themes/";
    }
    /**
     * Get the levels folder path into the server.
     * @return string
     */
    static function getLevelsPath(){
        return $_SERVER['DOCUMENT_ROOT']."/addons/levels/";
    }
    /**
     * Get the resources folder path into the server.
     * @return string
     */
    static function getResourcesPath(){
        return $_SERVER['DOCUMENT_ROOT']."/resources/";
    }
    /**
     * Get the name of the empty logo.
     * @return string
     */
    static function getEmptyLogoName(){
        return "logo-empty.png";
    }
    
}