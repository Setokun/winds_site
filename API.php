<?php
/**
 * Description of API file
 * @author Damien.D & Stephane.G
 */

/**
 * File used to interact with the java Winds game.<br><br>
 * USAGE : receives a GET request containing mandatory<br>
 * parameters (user e-mail address, user password and action<br>
 * defined in API_ACTION enumeration) and some other optionnal<br>
 * parameters depending the specified action.<br><br>
 * RETURN : a JSON-formated string as below :<br>
 * {error : the_error_message} or {data : returned_data}<br>
 * The returned data can be string or array (associative or not).
*/

require_once 'core/config.php';

$params = Tools::getIncomingParams();
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
$email	  = str_replace("\r\n","",$params['email']);
$password = $params['password'];
if(AccountController::checkIDs($email,$password) === FALSE){
    ApiController::displayResponse(NULL, "Bad identifiants");
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