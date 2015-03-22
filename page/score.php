<?php
include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";
require_once "../core/config.php";

$params = Tools::getParamsURL( $_SERVER['QUERY_STRING'] );
?>

<script type="text/javascript" src="../js/score.js" ></script>

<section id="score" style="padding-bottom:20px" class="col-sm-8 col-md-9 col-lg-10">
    <?php if( empty($params) ){ ?>
			<div class="align-mobile-left">
				<h2>Main ranking</h2>
				<table class="table table-bordered">
					<?php ScoreController::displayHeaders(); ?>
					<?php ScoreController::displayRanking(); ?>
				</table>
			</div>

        <div class="align-mobile-left">
			<h3>Ranking by level
				<span class="label-clickme">(Click on one item to show more details)</span>
			</h3>
			<h4>Basic levels</h4>
			<table class="table">
				<?php ScoreController::displayScoredBasicLevels(); ?>
			</table>
			<h4>Custom levels</h4>
			<table class="table">
				<?php ScoreController::displayScoredCustomLevels(); ?>
			</table>
        </div>
        

    <?php }else{
    $level = LevelManager::init()->getByID($params['id']); ?>
    <button class="btn btn-primary">Back</button>
    <?php ScoreController::displayInfosScore($level); ?>
    <table class="table table-bordered">
        <?php ScoreController::displayHeaders($level); ?>
        <?php ScoreController::displayRanking($level); ?>
    </table>
    <?php } ?>
</section>

<?php
include "../common/footer.php";
?>