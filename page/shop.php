<?php
session_start();
require_once "../core/config.php";
isset($_SESSION['user']) ? $user = User::initFrom($_SESSION['user']) : Tools::goToLogin();

include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";
?>

<script type="text/javascript" src="../js/shop.js" ></script>
    <section class="col-sm-8 col-md-9 col-lg-10">
        <!-- download button
        =============================-->
        <div id="download-btn" class="row">
            <button data-toggle="modal" href="#infos" class="btn btn-primary modal-wide center-block"><span class="glyphicon glyphicon-download-alt"></span> Download Game</button>
            <div class="modal" id="infos">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times</button>
                            <h4 class="modal-title">Download Game</h4>
                        </div>
                        <div class="modal-body">
                            <p>First, you need to have JRE installed on your computer.<br/>
                                You can download it <a href="http://www.oracle.com/technetwork/java/javase/downloads/jre8-downloads-2133155.html" target="_blank">here</a></p>
                            <p>To download the game, please follow this <a href="#">link</a>.</p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-info" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	
        <!-- themes table
        =============================-->
        <div style="margin:0 -15px;" class="th-winds">
            <h3>Available themes</h3>
        </div>
        <div class="row row-scroll">
            <table class="table">
                <div>
                    <?php AddonController::displayThemes(); ?>
                </div>
            </table>
        </div>

        <!-- basic levels table
        =============================-->
        <div style="margin:0 -15px;" class="th-winds">
            <h3>Available basic levels</h3>
        </div>
        <div class="row row-scroll">
            <table class="table">
                <div>
                    <?php AddonController::displayBasicLevels(); ?>
                </div>
            </table>
        </div>
        
        <!-- custom levels table
        =============================-->
        <div style="margin:0 -15px;" class="th-winds">
            <h3>Available custom levels <em style="font-size: 0.5em">(click&nbsp;on&nbsp;one&nbsp;item&nbsp;to&nbsp;show&nbsp;more&nbsp;details)</em></h3>
        </div>
        <div class="row row-scroll">
            <table class="table">
                <div>
                    <?php AddonController::displayCustomLevels("shop"); ?>
                </div>
            </table>
        </div>
</section>

<?php
include "../common/footer.php";
?>