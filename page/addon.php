<?php
include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";
require_once "../core/config.php";
?>

<section style="padding-bottom:20px" class="col-sm-8 col-md-9 col-lg-10">

    <div id="upload-new-addon">
        <article class="col-xs-12 ">
			<h2>Upload a new addon</h2>
            <form method="post" >
                <div class="col-xs-12 form-row">
						<div class="col-xs-12 col-md-3"><label>Name :</label></div>
						<div class="col-xs-12 col-md-9"><input class="form-control" type="text" placeholder="add-on name"></div>
					</div>
					<div class="col-xs-12 form-row">
						<div class="col-xs-12 col-md-3"><label>Description :</label></div>
						<div class="col-xs-12 col-md-9"><input class="form-control" type="text" placeholder="add-on description"></div>
					</div>
					<div class="col-xs-12 form-row">
						<div class="col-xs-12 col-md-3"><label>Type :</label></div>
						<div class="col-xs-12 col-md-4">
							<select class="form-control">
								<option value="-1">Type of addon</option>
								<option value="theme">Theme</option>
								<option value="level">Level</option>
							</select>
						</div>
					</div>
					<div class="col-xs-12 form-row">
							<input type="file" class="custom-file-input">
					</div>
					<div class="col-xs-12 form-row">
						<button id="btn-upload" style="margin-bottom: 20px;" class="pull-right btn btn-success" type="submit">Upload</button>
					</div>
            </form>
        </article>
        
        <article>
			<table class="table">
				<tr><th colspan="100%" class="th-winds">Available custom levels</th></tr>
				<?php AddonController::displayCustomLevels("addon"); ?>
			</table>
			<div class="col-xs-12 form-row">
				<button id="btn-remove" style="margin-bottom: 10px;" class="pull-right btn btn-danger" type="submit">Remove</button>
			</div>
        </article>
    </div>
</section>

<?php
include "../common/footer.php";
?>