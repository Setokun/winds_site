<?php
include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";
require_once "../core/config.php";

$_SESSION['user'] = UserManager::init()->getByID(8);
$user = $_SESSION['user'];
?>

<script type="text/javascript" src="../js/addon.js" ></script>
<section style="padding-bottom:20px" class="col-sm-8 col-md-9 col-lg-10">

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
    
    <article id="upload-addon" class="col-xs-12">
        <h2>Upload a new addon</h2>
        <div class="col-xs-12 form-row">
            <div class="col-xs-12 col-md-3">
                <label>Name :</label>
            </div>
            <div class="col-xs-12 col-md-9">
                <input id="addon-name" class="form-control" type="text" placeholder="Addon name">
            </div>
        </div>
        <div class="col-xs-12 form-row">
            <div class="col-xs-12 col-md-3">
                <label>Description :</label>
            </div>
            <div class="col-xs-12 col-md-9">
                <input id="addon-description" class="form-control" type="text" placeholder="Addon description">
            </div>
        </div>
        <div class="col-xs-12 form-row">
            <div class="col-xs-12 col-md-3">
                <label>Type :</label>
            </div>
            <div class="col-xs-12 col-md-4">
                <select id="addon-type" class="form-control">
                    <option value="-1">Type of addon</option>
                    <option value="theme">Theme</option>
                    <option value="level">Level</option>
                </select>
            </div>
        </div>
        <div class="col-xs-12 form-row">
            <input id="addon-file" type="file" class="custom-file-input">
        </div>
        <div class="col-xs-12 form-row">
            <button id="btn-upload" style="margin-bottom: 20px;" class="pull-right btn btn-success" type="submit">Upload</button>
        </div>
    </article>
        
    <article id="remove-addon" class="col-xs-12">
        <table class="table">
            <tr><th colspan="100%" class="th-winds">Available custom levels</th></tr>
            <?php AddonController::displayCustomLevels("addon"); ?>
        </table>
        <div class="col-xs-12 form-row">
            <button id="btn-remove" style="margin-bottom: 10px;" class="pull-right btn btn-danger" type="submit">Remove</button>
        </div>
    </article>
</section>

<?php
include "../common/footer.php";
?>