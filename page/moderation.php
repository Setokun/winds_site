<?php
include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";
require_once "../core/config.php";
?>

<section id="moderate" class="col-sm-8 col-md-9 col-lg-10">
        <article class="col-xs-12">
            <table id="table-moderate" class="table">
            <tr><th colspan="100%" class="th-winds">Custom levels to moderate</th></tr>
                <?php AddonController::displayLevelsToModerate(); ?>
            </table>
        </article>
</section>

<?php
include "../common/footer.php";
?>