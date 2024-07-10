<nav class="nav">
    <div class="nav-link-stable">
        <div class="nav-logo">
            <div class="nav-mobile-top-section">
                <a href="/">
                    <img class="nav-slogan" src="/default/images/grind_slogan.png" alt="THe grind slogan.">
                </a>
                <i class="bi bi-list nav-toggle-icon" onclick="navToggle()"></i>
            </div>
        </div>
        <form action="<?= getRouteUsingRouteName('show-public-course') ?>" method="get">
            <div class="nav-search">
                <input class="input search-input" name="title" type="text">
                <button class="nav-icon-div" style="border: none;">
                    <i class="bi bi-search nav-icon"></i>
                </button>
            </div>
        </form>
    </div>
    <div class="nav-link">
        <div class="nav-inner-links">

            <div class="nav-inner-link">
                <a class="btn link-plain <?= echoClassCurrentRouteSame('welcome', 'active-nav') ?>" href="<?= getRouteUsingRouteName('welcome') ?>">Home</a>
            </div>

            <div class="nav-inner-link">
                <a class="btn link-plain <?= echoClassCurrentRouteSame('show-public-course', 'active-nav') ?>" href="<?= getRouteUsingRouteName('show-public-course') ?>">Courses</a>
            </div>

            <!-- If Auth -->
            <?php if ($ifAuth) : ?>

                <div class="relative">
                    <a class="nav-inner-link flex aic" onclick="dropToggleNav('nav-profile')">
                        <img src="<?= $_SESSION['auth']['profile_image'] ?>" class="profile-img" alt="">
                    </a>

                    <div id="sub-menu-nav-profile" class="absolute sub-menu-nav">
                        <ul class="sub-menu-nav-ul shadow">

                            <!-- Profile -->
                            <a class="link-plain text-white" href="<?= getRouteUsingRouteName('profile') ?>">
                                <li class="flex g-mid">
                                    <i class="bi bi-person-circle"></i> Profile
                                </li>
                            </a>

                            <!-- Logout -->
                            <form action="<?php echo getRouteUsingRouteName("logout") ?>" method="POST">
                                <button type="submit" class="btn-plain">
                                    <li class="flex g-mid">
                                        <i class="bi bi-box-arrow-left"></i> Logout
                                    </li>
                                </button>
                            </form>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Not Auth -->
            <?php if (!$ifAuth) : ?>
                <div class="nav-inner-link">
                    <div class="flex jcc">
                        <a class="btn half-left-round link-plain" href="<?php echo getRouteUsingRouteName("show-login") ?>">Login</a>
                        <a class="btn half-right-round link-plain" href="<?php echo getRouteUsingRouteName("show-register") ?>">Register</a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Admin -->
            <?php if ($ifAuthAdmin) : ?>
                <div class="nav-inner-link">
                    <a class="btn link-plain <?= checkCurrentRouteContains('/admin') ? 'active-nav' : '' ?>" href="<?php echo getRouteUsingRouteName("show-course") ?>">
                        <i class="bi bi-speedometer"></i> Dashboard</a>
                </div>
            <?php endif; ?>

        </div>
    </div>
</nav>