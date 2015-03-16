<?php
include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";
require_once "../core/config.php";
?>

<!--<link type='text/css' rel='stylesheet' href='../css/home.css' />-->
<section style="padding-bottom:20px" class="col-sm-12 col-md-10">
          <p>Welcome to your home page</p>

    <div id="news">
        <article class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <table id="news-addon" class="table table-bordered">
                <tr><th>Last events of the addons</th></tr>
                <?php AddonController::displayLastNews(); ?>
            </table>
        </article>
        <article class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <table id="news-forum" class="table table-bordered">
                <tr><th>Last events of the forum</th></tr>
                <?php ForumController::displayLastNews(); ?>
            </table>
        </article>
    </div>
</section>

<?php
include "../common/footer.php";
?>
