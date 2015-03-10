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

// -- Check the presence of mandatory parameters --
if(!$hasMandatoryParams){
    ApiController::displayResponse(NULL, "Missing mandatory parameters");
}

// -- Check the match between IDs --
$email	  = $params['email'];
$password = $params['password'];
if(AccountController::checkIDs($email,$password) === FALSE){
    ApiController::displayResponse(NULL, "Bad identifiants");
}

// -- Check if the asked action exists --
$action = $params['action'];
if( ApiController::existsAction($action) === FALSE){
    ApiController::displayResponse(NULL, "Unknown action");
}

// -- Get the result for the asked action --
$user = AccountController::getUserProfile($email);
ApiController::$action($user,$params);