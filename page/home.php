<?php
session_start();
require_once "../core/config.php";
isset($_SESSION['user']) ? $user = User::initFrom($_SESSION['user']) : Tools::goToLogin();

include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";
?>

<section class="col-sm-8 col-md-9 col-lg-10">
    <div id="news">
        <article class="align-mobile-left col-sm-12 col-md-6">
            <table id="news-addon" class="table table-bordered">
                <tr><th colspan="100%" class="th-winds">Last events of the add-ons</th></tr>
                <?php AddonController::displayLastNews(); ?>
            </table>
        </article>
        <article class="align-mobile-left col-sm-12 col-md-6">
            <table id="news-forum" class="table table-bordered">
                <tr><th colspan="100%" class="th-winds">Last events of the forum</th></tr>
                <?php ForumController::displayLastNews(); ?>
            </table>
        </article>
    </div>
</section>

<?php
include "../common/footer.php";
?>
