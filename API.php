<?php
/*
PRINCIPE :
recoit une requete en GET composée des paramètres obligatoires :
	- user email
	- user password
	- action (constante de API_ACTION)
et d'autres optionnels selon l'action désirée

RETOUR : JSON
{error: message d'erreur} ou {data: valeur des données à retourner}
*/
require_once 'core/config.php';

$params = Tools::getParamsURL( $_SERVER['QUERY_STRING'] );
$hasMandatoryParams = isset($params['email']) && isset($params['password']) && isset($params['action']);

if(!$hasMandatoryParams){
	$params = Tools::getParams($_REQUEST);
	$hasMandatoryParams = isset($params['email']) && isset($params['password']) && isset($params['action']);
}

// -- Check the presence of mandatory parameters --
if(!$hasMandatoryParams){
    ApiController::displayResponse(NULL, "Missing mandatory parameters");
}

// -- Check the match between IDs --
//$email	  = substr($params['email'], 0, -2);
$email	  = str_replace("\r\n","",$params['email']);
$password = $params['password'];
if(AccountController::checkIDs($email,$password) === FALSE){
    //ApiController::displayResponse(NULL, "Bad identifiants");
	ApiController::displayResponse(NULL, "Identifiant: ".$email." et pwd: ".$password);
}

// -- Check if the asked action exists --
$action = $params['action'];
if( ApiController::existsAction($action) === FALSE){
    ApiController::displayResponse(NULL, "Unknown action");
}

// -- Check if the current account has been banished
$user = AccountController::getUserProfile($email);
if($user->isBanished()){
    ApiController::displayResponse(NULL, "Banished account");
}

// -- Get the result for the asked action --
ApiController::$action($user,$params);