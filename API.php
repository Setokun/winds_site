<?php
/*
PRINCIPE :
recoit une requete en GET composée des paramètres obligatoires :
	- user email
	- user password
	- action (constante de API_ACTION)
et d'autres optionnels selon l'action désirée

RETOUR :
format JSON

A DEFINIR :
le moyen de transfert les addons (format JAR)
*/
require_once 'core/config.php';

$params = Tools::getParamsURL( $_SERVER['QUERY_STRING'] );
$hasMandatoryParams = isset($params['email']) && isset($params['password']) && isset($params['action']);


// -- Check the presence of mandatory parameters --
if(!$hasMandatoryParams){
    echo "Missing mandatory parameters";
    die;
}
// -- Check the match between IDs --
$email	  = $params['email'];
$password = $params['password'];
if(AccountController::checkIDs($email,$password) === FALSE){
    echo "Bad identifiants";
    die;
}
// -- Get the user's account
$user = AccountController::getUserAccount($email);
// -- Check if the asked action exists --
$action = $params['action'];
if( ApiController::existsAction($action) === FALSE){
    echo "Unknown action";
    die;
}


// -- Get the result for the asked action --
$result = NULL;
switch ($action){
    case API_ACTION::GET_THEME :                 $result = ApiController::getThemes(); break;
    case API_ACTION::GET_CUSTOM_LEVELS :         $result = ApiController::getCustomLevels(); break;
    case API_ACTION::GET_LEVELS_TOMODERATE :     $result = ApiController::getLevelsToModerate(); break;
    case API_ACTION::GET_RANKS :                 $result = ApiController::getRanks($user->getId()); break;
    case API_ACTION::DOWNLOAD_USER_ACCOUNT :     $result = ApiController::downloadUserAccount($user->getId()); break;
    case API_ACTION::DOWNLOAD_THEME :            $result = ApiController::downloadTheme($idTheme); break;
    case API_ACTION::DOWNLOAD_CUSTOM_LEVEL :     $result = ApiController::downloadCustomLevel($idLevel); break;
    case API_ACTION::DOWNLOAD_LEVEL_TOMODERATE : $result = ApiController::downloadLevelToModerate($idLevel); break;
    case API_ACTION::UPLOAD_CUSTOM_LEVEL :       $result = ApiController::uploadCustomLevel(); break;
    case API_ACTION::UPLOAD_SCORES :             $result = ApiController::uploadScores($user->getId(), NULL); break;
}
echo $result;