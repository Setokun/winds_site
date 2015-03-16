        <div class="row"><script src="../js/jquery-2.1.1.js">alert();</script>
            <nav id="navigation" style="background: #ccc; z-index: 1; position:relative" class="col-sm-3 col-md-2">
		
          <ul id="menu">
                <li>Hello<br><?php echo $user->pseudo; ?></li>
                <hr/>
                <a id="profile" href="profile.php"><li class="category">Profile</li></a>
                <a id="logout" href="#"><li>Log out</li></a>
                <hr/>
                <a id="home" href="home.php"><li class="category">Home</li></a>
                <a id="shop" href="shop.php"><li class="category">Shop</li></a>
                <a id="score" href="score.php"><li class="category">Scores</li></a>
                <a id="forum" href="forum.php"><li class="category">Forum</li></a>
                <?php if($user->userType > 0){ ?>
                <a id="account" href="account.php"><li class="category">Accounts</li></a>
                <a id="moderation" href="moderation.php"><li class="category">Moderation</li></a>
                <?php }
                if($user->userType > 1){ ?>
                <a id="addon" href="addon.php"><li class="category">Addons</li></a>
                <?php } ?>
            </ul>
        </nav>
            <div id="menu-small" class="btn-group"> 
			<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Menu <span class="caret"></span></button>
			<ul class="dropdown-menu">
                            <li class="dropdown-header">Hello <?php echo $user->pseudo; ?></li>
                            <li><a href="#"></a></li>
                            <li><a href="#"><span class="glyphicon glyphicon-user"></span> Profile</a></li>
                            <li><a id="logout" href="#"><span class="glyphicon glyphicon-log-out"></span> Log out</a></li>
                            <li class="divider"></li>
                            <li><a href="#"><span class="glyphicon glyphicon-home"></span> Home</a></li>
                            <li><a href="#"><span class="glyphicon glyphicon-shopping-cart"></span> Shop</a></li>
                            <li><a href="#"><span class="glyphicon glyphicon-screenshot"></span> Scores</a></li>
                            <li><a href="#"><span class="glyphicon glyphicon-book"></span> Forum</a></li>
                            <?php if($user->userType > 0){ ?>
                            <li class="divider"></li>
                            <li class="dropdown-header"> Management</li>
                            <li><a href="#"><span class="glyphicon glyphicon-user"></span> Accounts</a></li>
                            <li><a href="#"><span class="glyphicon glyphicon-check"></span> Moderation</a></li>
                            <?php }
                            if($user->userType > 1){ ?>
                            <li><a href="#"><span class="glyphicon glyphicon-plus-sign"></span> Addons</a></li>
                            <?php } ?>
			</ul>
		</div>