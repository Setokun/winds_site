<?php
include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";
require_once "../core/config.php";
?>

<script type="text/javascript" src="../js/shop.js" ></script>
		<section id="shop" class="col-sm-8 col-md-9 col-lg-10">
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
								To download the game, please follow this <a href="#">link</a>.
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
			<div class="row">
				<table class="table">
					<tr>
						<th colspan="100%" class="th-winds">Available themes</th>
					</tr>
					<?php AddonController::displayThemes(); ?>
				</table>
			</div>
	
			<!-- custom levels table
			=============================-->
			<div class="row">
				<table class="table">
					<tr>
						<th colspan="100%" class="th-winds">Available custom levels <em>(click on one item to show more details)</em></th>
					</tr>
					<?php AddonController::displayCustomLevels("shop"); ?>
				</table>
			</div>
</section>

<?php
include "../common/footer.php";
?>