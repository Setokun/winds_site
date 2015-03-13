<?php
include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";
require_once "../core/config.php";
?>

<link type="text/css" rel="stylesheet" href="../css/shop.css">
<script type="text/javascript" src="../js/shop.js" ></script>
<section id="shop" style="padding-bottom:20px" class="col-sm-9 col-md-10">
    <div class="row">
        <button id="download-game" href="#">Click me to download the Winds game</button>
    </div>
    <div>
        <h3>Available themes</h3>
        <?php ThemeController::displayAll(); ?>
    </div>
    <div>
        <h3>Available custom levels
            <span class="label-clickme">(Click on one item to show more details)</span>
        </h3>
        <div>
            <?php LevelController::displayCustomLevels(); ?>
        </div>
    </div>
</section>

<?php
include "../common/footer.php";
?>