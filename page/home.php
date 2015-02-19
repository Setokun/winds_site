<?php
include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";
require_once "../core/config.php";
?>

<link type='text/css' rel='stylesheet' href='../css/home.css' />
<div id="homepage">
Welcome to your home page
    <div id="news">
        <table id="news-addon">
            <tr><th>Last events of the addons</th></tr>
            <?php AddonController::displayLastNews(); ?>
        </table>
        <table id="news-forum">
            <tr><th>Last events of the forum</th></tr>
            <?php ForumController::displayLastNews(); ?>
        </table>
    </div>
</div>

<?php
include "../common/footer.php";
?>
