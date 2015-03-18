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
    <article id="common" class="table-bordered col-xs-12">
        <input id="idUser" type="hidden" value="<?php echo $user->getId(); ?>" >
        <div id="loader" style="display:none" >
            <h4>Action in progress</h4>
            <img src="..\resources\loader.gif" style="height: 32px; width: 32px" >
            <h5>Please, wait.</h5>        
        </div>
        <div id="infos" style="display:none"/>
    </article>
    <article class="table-bordered col-xs-12">
        <p>Winds accounts</p>
        <div id="list-user" class="col-xs-12">
            <?php AccountController::displayList($user); ?>
        </div>
    </article>
    <article class="table-bordered col-xs-12">
        <p>Accounts waiting deletion - <em style="font-size:12px">click on an item to focus it in the previous list</em></p>
        <div id="list-deletion" class="col-xs-12">
            <?php AccountController::displayDeletionList($user); ?>
        </div>
    </article>
</section>

<?php
include "../common/footer.php";
?>