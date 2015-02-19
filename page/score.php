<?php
include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";
require_once "../core/config.php";

$params = Tools::getParamsURL( $_SERVER['QUERY_STRING'] );
?>

<link type="text/css" rel="stylesheet" href="../css/score.css">
<script type="text/javascript" src="../js/score.js" ></script>
<div id="score">
    <?php if( empty($params) ){ ?>
    <div>
        <h3>Main ranking</h3>
        <table>
            <?php ScoreController::displayHeaders(); ?>
            <?php ScoreController::displayRanking(); ?>
        </table>
    </div>
    <div>
        <h3>Ranking by level
            <span class="label-clickme">(Click on one item to show more details)</span>
        </h3>
        <h4>Basic levels</h4>
        <div>
            <?php ScoreController::displayBasicLevels(); ?>
        </div>
        <h4>Custom levels</h4>
        <div>
            <?php ScoreController::displayCustomLevels(); ?>
        </div>
    </div>
    <?php }else{ ?>
    <button class="btn-back">Back</button>
    <h4>Ranking</h4>
	<table>
            <?php ScoreController::displayHeaders(); ?>
            <?php ScoreController::displayRanking($params['id']); ?>
	</table>
    <?php } ?>
</div>

<?php
include "../common/footer.php";
?>