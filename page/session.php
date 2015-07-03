<?php
session_start();
require_once '../core/config.php';

if( isset($_POST['login']) ){
    // login
    $email  = isset($_POST['email']) ? $_POST['email'] : NULL;
    $pwd    = isset($_POST['password']) ? $_POST['password'] : NULL;
    $logged = AccountController::checkIDs($email, $pwd);
    
    if( !$logged ){ Tools::goToLogin(); }
    $_SESSION['user'] = AccountController::getUserProfile($email)->toAssocArray();
    Tools::goToHome();
}else if( isset($_POST['logout']) ){
    // logout
    session_unset();
    session_abort();
    Tools::goToLogin();
}else{
    // missing parameters
    Tools::goToLogin();
}