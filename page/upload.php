<?php
/**
 * Description of upload page
 * @author Damien.D & Stephane.G
 *
 * File used to manage theme and level uploads from addon page.
 */

require_once "../core/config.php";

$idUser    = $_POST["idUser"];
$addonName = htmlentities($_POST["addon-name"], ENT_QUOTES);
$addonDesc = htmlentities($_POST["addon-description"], ENT_QUOTES);
$addonType = $_POST["addon-type"];
$addonFile = $_FILES["addon-file"];

function checkRequirements(){
    global $addonFile;
    
    if(substr($addonFile['name'],-4,4) !== ".jar"){
        echo json_encode(['error' => "The choosen file is not a JAR file"],JSON_UNESCAPED_SLASHES);
        die;
    }    
    if( !ManagerDB::availableDB() ){
        echo json_encode(['DBdown' => "Unavailable database"],JSON_UNESCAPED_SLASHES);
        die;
    }
}
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
    Tools::displayResponse(  $idThm ? "Theme uploaded" : NULL,
        $idThm ? NULL : "Unable to store the theme in the database");
}
function uploadLevel(){
    global $addonFile;
    
    $manip = LevelManipulator::init($addonFile)->run(TRUE);
    Tools::displayResponse($manip->getResult(), $manip->getError());
}

checkRequirements();
if($addonType === "theme"){ uploadTheme(); }
if($addonType === "level"){ uploadLevel(); }
?>