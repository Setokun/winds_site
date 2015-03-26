<?php
include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";
require_once "../core/config.php";

$_SESSION['user'] = UserManager::init()->getByID(8);
$user = $_SESSION['user'];
?>

<script type="text/javascript" src="../js/account.js" ></script>
<section style="padding:20px" class="col-sm-9 col-md-10">
    
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
    
    <article id="users-list" class="col-xs-12">
        <h2>Winds accounts</h2>
        <?php AccountController::displayList($user); ?>
    </article>
    
    <article class="col-xs-12">
        <h2>Accounts waiting deletion <em style="font-size:12px">( click on an item to focus it in the previous list )</em></h2>
        <table id="deletions-list" class="table table-bordered">
            <?php AccountController::displayDeletionList($user); ?>
        </table>
    </article>
</section>

<?php
include "../common/footer.php";
?>