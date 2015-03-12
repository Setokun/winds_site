        <div class="row">
            <nav style="background: #ccc; z-index: 1; position:relative" class="col-sm-3 col-md-2">
		<div id="navigation" style=""></div>
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