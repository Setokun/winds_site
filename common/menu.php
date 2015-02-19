        <div id="menu">
            <ul class="nav nav-pills nav-stacked">
                <li><a id="user">Hello<br><?php echo $user->pseudo; ?></a></li>
                <hr>
                <li class="category"><a id="profile" href="profile.php">Profile</a></li>
                <li><a id="logout">Log out</a></li>
                <hr>
                <li class="category"><a href="home.php">Home</a></li>
                <li class="category"><a href="shop.php">Shop</a></li>
                <li class="category"><a href="score.php">Scores</a></li>
                <li class="category"><a href="forum.php">Forum</a></li>
                <?php if($user->userType > 0){ ?>
                <li class="category"><a href="account.php">Accounts</a></li>
                <li class="category"><a href="moderation.php">Moderation</a></li>
                <?php }
                if($user->userType > 1){ ?>
                <li class="category"><a href="addon.php">Addons</a></li>
                <?php } ?>
            </ul>
        </div>
        <div id="content">