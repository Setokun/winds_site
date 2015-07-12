<?php
/**
 * Description of session page
 * @author Damien.D & Stephane.G
 *
 * File used to manage user session onto Winds site.
 */

require_once '../core/config.php';

if( isset($_POST['login']) ){
    // login
    $email  = isset($_POST['email']) ? $_POST['email'] : NULL;
    $pwd    = isset($_POST['password']) ? $_POST['password'] : NULL;
    
    $logged = AccountController::checkIDs($email, $pwd);
    if( !$logged ){ Tools::goToLogin();die; }
    
    $profile = AccountController::getUserProfile($email);
    if($profile->getUserStatus() == USER_STATUS::CREATED){
        header("location: refused.php");die;
    }
    
    session_start();
    $_SESSION['user'] = $profile->toAssocArray();
    Tools::goToHome();
}else if( isset($_POST['logout']) ){
    // logout
    session_start();
    session_unset();
    session_abort();
    Tools::goToLogin();
}else{
    // missing parameters
    Tools::goToLogin();
}