<?php
/**
 * Description of score page
 * @author Damien.D & Stephane.G
 *
 * File used to interact with the user about level scores.
 */

session_start();
require_once "../core/config.php";
isset($_SESSION['user']) ? $user = User::initFrom($_SESSION['user']) : Tools::goToLogin();

include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";

$params = Tools::getIncomingParams();
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
        <h2>Ranking by level
            <span class="label-clickme">(Click&nbsp;on&nbsp;one&nbsp;item&nbsp;to&nbsp;show&nbsp;more&nbsp;details)</span>
        </h2>
        <h4 id="basic-scores-title">Basic levels</h4>
        <div class="row-scroll">
            <table class="table">
                <?php ScoreController::displayScoredBasicLevels(); ?>
            </table>
        </div>
        <h4>Custom levels</h4>
        <div class="row-scroll">
            <table class="table">
                <?php ScoreController::displayScoredCustomLevels(); ?>
            </table>
        </div>
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