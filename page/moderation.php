<?php
include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";
require_once "../core/config.php";

$_SESSION['user'] = UserManager::init()->getByID(8);

$params = Tools::getParamsURL( $_SERVER['QUERY_STRING'] );
$user   = $_SESSION['user'];
?>

<script type="text/javascript" src="../js/moderation.js" ></script>
<section id="moderation" class="col-sm-8 col-md-9 col-lg-10">
    <div id="div-ajax">
        <div id="ajax-loader" style="display:none" >
            <h4>Action in progress</h4>
            <img src="..\resources\loader.gif" style="height: 32px; width: 32px" >
            <h5>Please, wait.</h5>        
        </div>
        <div id="ajax-message" style="display:none"></div>
    </div>
    <article class="col-xs-12">
        <table id="table-moderate" class="table">
            <tr><th colspan="100%" class="th-winds">Custom levels to moderate</th></tr>
            <?php AddonController::displayLevelsToModerate(); ?>
        </table>
    </article>
</section>

<?php
include "../common/footer.php";
?>