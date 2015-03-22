<?php
include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";
require_once "../core/config.php";
?>

		<section class="col-sm-8 col-md-9 col-lg-10">
			
			<div class="col-sm-12">
				<h2>My Winds account</h2>
				<form role="form">
					<div class="col-xs-12 form-row">
						<div class="col-xs-12 col-md-3"><label>E-mail adress</label></div>
						<div class="col-xs-12 col-md-9"><input class="form-control" type="email" disabled placeholder="toto@example.com"></div>
					</div>
					<div class="col-xs-12 form-row">
						<div class="col-xs-12 col-md-3"><label>Pseudo :</label></div>
						<div class="col-xs-12 col-md-9"><input class="form-control" type="text" disabled placeholder="Player1"></div>
					</div>
					<div class="col-xs-12 form-row">
						<button style="margin-bottom: 10px;" class="pull-left btn btn-primary" type="submit">Change password</button>
						<button style="margin-bottom: 10px;" class="pull-right btn btn-danger" type="submit">Ask account deletion</button>
					</div>
				</form>
			</div>
			
		</section>

<?php
include "../common/footer.php";
?>