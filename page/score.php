<?php
include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";
require_once "../core/config.php";

$params = Tools::getParamsURL( $_SERVER['QUERY_STRING'] );
?>

<link type="text/css" rel="stylesheet" href="../css/score.css">
<script type="text/javascript" src="../js/score.js" ></script>
<section id="score" style="padding-bottom:20px" class="col-sm-9 col-md-10">
    <?php if( empty($params) ){ ?>
    <div>
        <h3>Main ranking</h3>
        <table class="table table-bordered">
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
            <?php ScoreController::displayScoredBasicLevels(); ?>
        </div>
        <h4>Custom levels</h4>
        <div>
            <?php ScoreController::displayScoredCustomLevels(); ?>
        </div>
    </div>
    <?php }else{ ?>
    <button class="btn-back button-blue">Back</button>
    <h4>Ranking</h4>
	<table class="table table-bordered">
            <?php ScoreController::displayHeaders($params['id']); ?>
            <?php ScoreController::displayRanking($params['id']); ?>
	</table>
    <?php } ?>
</section>

<?php
include "../common/footer.php";
?>