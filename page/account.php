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
    <article id="common" class="col-xs-12">
        <input id="idUser" type="hidden" value="<?php echo $user->getId(); ?>" >
        <div id="loader" style="display:none" >
            <h4>Action in progress</h4>
            <img src="..\resources\loader.gif" style="height: 32px; width: 32px" >
            <h5>Please, wait.</h5>        
        </div>
        <div id="infos" style="display:none" ></div>
    </article>
	
    <article id="accounts-list" class="col-xs-12">
        <h2>Winds accounts</h2>
        <?php AccountController::displayList($user); ?>
    </article>
	
    <article class="col-xs-12">
        <h2>Accounts waiting deletion - <em style="font-size:12px">click on an item to focus it in the previous list</em></h2>
		<table class="table table-bordered">
		
            <?php AccountController::displayDeletionList($user); ?>
		</table>
    </article>
</section>

<?php
include "../common/footer.php";
?>