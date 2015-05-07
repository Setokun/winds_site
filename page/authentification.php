<?php
session_start();
require_once '../core/config.php';


if( empty($_POST) ){
    Tools::goToLogin();
}else{
    $email  = isset($_POST['email']) ? $_POST['email'] : NULL;
    $pwd    = isset($_POST['password']) ? $_POST['password'] : NULL;
    $logged = AccountController::checkIDs($email, $pwd);
    if( !$logged ){ Tools::goToLogin(); }

    $_SESSION['user'] = json_decode(json_encode(
            AccountController::getUserProfile($email)), TRUE);
    Tools::goToHome();
}


