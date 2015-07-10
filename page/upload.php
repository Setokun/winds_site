<?php

require_once "../core/config.php";

$idUser    = $_POST["idUser"];
$addonName = $_POST["addon-name"];
$addonDesc = $_POST["addon-description"];
$addonType = $_POST["addon-type"];
$addonFile = $_FILES["addon-file"];

function uploadTheme(){
    global $idUser, $addonName, $addonDesc, $addonFile;
    
    $dest = Tools::getThemesPath().$addonFile['name'];
    if( file_exists($dest) ){ Tools::displayResponse(NULL, "The theme "
            ."file named \"".$addonFile['name']."\" already exists"); }
    
    $moved = rename($addonFile['tmp_name'], $dest);
    if( !$moved ){ Tools::displayResponse(NULL, "Unable to "
                ."store the theme on the remote server"); }
    
    $manip = ThemeManipulator::init($dest, $addonName);
    $logoName = $manip->getLogoName();
    $dest = str_replace($_SERVER['DOCUMENT_ROOT'].'/', '', $dest);

    $theme = Theme::init($addonName, $addonDesc, $dest, $logoName, $idUser);
    $idThm = ThemeManager::init()->insert($theme);
    Tools::displayResponse(  $idThm ? "Theme added" : NULL,
        $idThm ? NULL : "Unable to store the theme in the database");
}
function uploadLevel(){
    /*global $addonFile;
    
    //$manip = LevelManipulator::init($addonFile, TRUE)->run();
    //Tools::displayResponse($manip->getResult(), $manip->getError());*/
}

if($addonType === "theme"){ uploadTheme(); }
if($addonType === "level"){ uploadLevel(); }

?>