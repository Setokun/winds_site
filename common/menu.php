		<div id="page" class="container">
		<div class="col-sm-4 col-md-3 col-lg-2">
			<nav class="navbar navbar-default" role="navigation">
				
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>

				<div class="collapse navbar-collapse">
					<div class="panel-heading">Hello <em><?php echo $user->pseudo; ?></em></div>
					<ul class="nav nav-stacked">
						<li><a id="profile" href="profile.php"><span class="glyphicon glyphicon-user"></span> Profile</a></li>
						<li><a id="logout" href="#"><span class="glyphicon glyphicon-log-out"></span> Log out</a></li>
						<hr>
						<li><a id="home" href="home.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
						<li><a id="shop" href="shop.php"><span class="glyphicon glyphicon-shopping-cart"></span> Shop</a></li>
						<li><a id="score" href="score.php"><span class="glyphicon glyphicon-screenshot"></span> Scores</a></li>
						<li><a id="forum" href="forum.php"><span class="glyphicon glyphicon-book"></span> Forum</a></li>
						<?php if($user->userType > 0){ ?>
						<hr>	
						<li><a id="account" href="account.php"><span class="glyphicon glyphicon-user"></span> Accounts</a></li>
						<li><a id="moderation" href="moderation.php"><span class="glyphicon glyphicon-check"></span> Moderation</a></li>
						<?php }
						if($user->userType > 1){ ?>
						<li><a id="addon" href="addon.php"><span class="glyphicon glyphicon-plus-sign"></span> Addons</a></li>
						<?php } ?>
					</ul>
				</div>
			</nav>
		</div>