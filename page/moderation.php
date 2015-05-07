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
<section class="col-sm-8 col-md-9 col-lg-10">
    
    <article class="modal" id="ajax">
        <input id="idUser" type="hidden" value="<?php echo $user->getId(); ?>" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button id="ajax-close" type="button" class="close" data-dismiss="modal">&times</button>
                    <h4 class="modal-title">Action in progress</h4>
                </div>
                <div class="modal-body">
                    <div id="ajax-loader" style="text-align:center">
                        <img src="..\resources\loader.gif">
                        <h5 style="margin-bottom:0"><b>Please, wait.</b></h5>
                    </div>
                    <div id="ajax-message"></div>
                </div>
                <div class="modal-footer">
                    <button id="ajax-closer" class="btn" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </article>
    
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