<?php
include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";
require_once "../core/config.php";
?>

<section style="padding-bottom:20px" class="col-sm-9 col-md-10">
    <div id="moderate">
        <article class="col-xs-12">
            <table id="table-moderate" class="table">
                <tr><th colspan="3">Custom levels to moderate</th></tr>
                <?php AddonController::displayLevelsToModerate(); ?>
            </table>
        </article>
    </div>
</section>

<?php
include "../common/footer.php";
?>