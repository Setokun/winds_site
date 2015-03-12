<?php
include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";
require_once "../core/config.php";
?>

<link type="text/css" rel="stylesheet" href="../css/shop.css">
<script type="text/javascript" src="../js/shop.js" ></script>
<div id="shop">
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
</div>

<?php
include "../common/footer.php";
?>