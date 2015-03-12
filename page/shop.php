<?php
include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";
require_once "../core/config.php";
?>

<link type="text/css" rel="stylesheet" href="../css/shop.css">
<script type="text/javascript" src="../js/shop.js" ></script>
<section id="shop" style="padding-bottom:20px" class="col-sm-9 col-md-10">
    <div>
        <h4>Available themes</h4>
        <?php ThemeController::displayAll(); ?>
    </div>
    <div>
        <h4>Available custom levels
            <span class="label-clickme">(Click on one item to show more details)</span>
        </h4>
        <div>
            <?php LevelController::displayCustomLevels(); ?>
        </div>
    </div>
</section>

<?php
include "../common/footer.php";
?>