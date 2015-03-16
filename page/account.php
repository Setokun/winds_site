<?php
include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";
require_once "../core/config.php";

$_SESSION['user'] = UserManager::init()->getByID(8);
$user = $_SESSION['user'];
?>

<section style="padding:20px" class="col-sm-9 col-md-10">
    <article class="table-bordered col-xs-12">
        <p>Winds accounts</p>
        <div class="col-xs-12">
            <?php AccountController::displayList($user); ?>
        </div>
    </article>
    <article class="table-bordered col-xs-12">
        <p>Accounts waiting deletion - <em style="font-size:12px">click on an item to focus it in the previous list</em></p>
        <div class="col-xs-12">
            <?php AccountController::displayDeletionList($user); ?>
        </div>
    </article>
</section>

<?php
include "../common/footer.php";
?>