<?php
session_start();
require_once "../core/config.php";
isset($_SESSION['user']) ? $user = User::initFrom($_SESSION['user']) : Tools::goToLogin();

include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";
?>

<script type="text/javascript" src="../js/profile.js" ></script>
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

    <article id="profile" class="col-sm-12">
        <h2>My Winds account</h2>
        <div class="col-xs-12 form-row">
            <div class="col-xs-12 col-md-3">
                <label>E-mail adress</label>
            </div>
            <div class="col-xs-12 col-md-9">
                <input class="form-control" type="email" disabled
                       value="<?php echo $user->getEmail(); ?>">
            </div>
        </div>
        <div class="col-xs-12 form-row">
            <div class="col-xs-12 col-md-3">
                <label>Pseudo :</label>
            </div>
            <div class="col-xs-12 col-md-9">
                <input class="form-control" type="text" disabled
                       value="<?php echo $user->getPseudo(); ?>">
            </div>
        </div>
        <div class="col-xs-12 form-row">
            <div class="col-xs-12 col-md-3">
                <label>Status :</label>
            </div>
            <div class="col-xs-12 col-md-9">
                <input class="form-control" type="text" disabled
                       value="<?php echo Tools::capitalize($user->getUserStatus()); ?>">
            </div>
        </div>
        <div class="col-xs-12 form-row">
            <button id="change-pwd" class="pull-left btn btn-primary"
                    style="margin-bottom: 10px;" >Change password</button>
            <button id="account-deletion" class="pull-right btn btn-danger"
                    style="margin-bottom: 10px;" >Ask account deletion</button>
        </div>
    </article>

</section>

<?php
include "../common/footer.php";
?>