<nav class="nav">
    <div class="nav-link-stable">
        <div class="nav-logo">
            <div class="nav-mobile-top-section">
                <a href="/">
                    <img class="nav-slogan" src="./default/images/grind_slogan.png" alt="THe grind slogan.">
                </a>
                <i class="bi bi-list nav-toggle-icon" onclick="navToggle()"></i>
            </div>
        </div>
        <div class="nav-search">
            <input class="input search-input" type="text">
            <div class="nav-icon-div">
                <i class="bi bi-search nav-icon"></i>
            </div>
        </div>
    </div>
    <div class="nav-link">
        <div class="nav-inner-links">
            <div class="nav-inner-link">
                <a class="btn link-plain" href="">Courses</a>
            </div>
            <div class="nav-inner-link">
                <a class="btn link-plain" href="">Explore</a>
            </div>
            <div class="nav-inner-link">
                <a class="btn link-plain" href="">Profile</a>
            </div>

            <!-- Not Auth -->
            <?php if (!$ifAuth) : ?>
                <div class="nav-inner-link">
                    <div class="flex jcc">
                        <a class="btn half-left-round link-plain" href="<?php echo getRouteUsingRouteName("show-login") ?>">Login</a>
                        <a class="btn half-right-round link-plain" href="<?php echo getRouteUsingRouteName("show-register") ?>">Register</a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Auth -->
            <?php if ($ifAuth) : ?>
                <div class="nav-inner-link">
                    <form action="<?php echo getRouteUsingRouteName("logout") ?>" method="POST">
                        <input type="submit" class="btn link-plain" value="Logout">
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>