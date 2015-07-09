<?php
/*
require_once "../core/config.php";

$idUser    = $_POST["idUser"];
$addonName = $_POST["addon-name"];
$addonDesc = $_POST["addon-description"];
$addonType = $_POST["addon-type"];
$addonFile = $_FILES["addon-file"];

function uploadTheme(){
    global $idUser, $addonName, $addonDesc, $addonFile;*/
    var_dump($dest, $moved); die;
    /*$dest = Tools::getThemesPath().$addonFile['name'];
    
    /*$moved = rename($addonFile['tmp_name'], $dest);
    if( !$moved ){ Tools::displayResponse(NULL, "Unable to "
                ."store the theme on the remote server"); }

    /*$manip = ThemeManipulator::init($dest, $addonName);
    var_dump($manip->getLogoPath());*/
    /*$theme = Theme::init($addonName, $addonDesc, $dest, NULL, $idUser);
    $idThm = ThemeManager::init()->insert($theme);
    Tools::displayResponse( $idThm ? "Theme added" : NULL,
        $idThm ? NULL : Tools::displayResponse(NULL, "Unable"
                ." to store the theme in the database"));
}
function uploadLevel(){
    global $addonFile;
    
    //$manip = LevelManipulator::init($addonFile, TRUE)->run();
    //Tools::displayResponse($manip->getResult(), $manip->getError());
}

if($addonType === "theme"){ uploadTheme(); }
if($addonType === "level"){ uploadLevel(); }

?>