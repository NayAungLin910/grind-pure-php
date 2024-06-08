<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Head -->
    <?php require_once("../src/Views/components/head.php")  ?>

    <title>Grind | Sign Up</title>
</head>

<body>

    <!-- Nav -->
    <?php require_once("../src/Views/components/nav.php") ?>

    <!-- Main -->
    <div class="main">

        <!-- Login Card -->
        <div class="card login-card">

            <form action="/register" method="POST" enctype="multipart/form-data">

                <!-- Card Header -->
                <h3 class="card-header">Sign Up</h3>

                <!-- Name -->
                <div class="form-group">
                    <label class="form-label text-white" for="name">Name</label>
                    <input class="input form-input" id="name" name="name" value="<?php displayFlashedSessionValue('old', 'name') ?>" type="name">
                    <?php displayErrorMessage("name") ?>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label class="form-label text-white" for="email">Email</label>
                    <input class="input form-input" id="email" name="email" value="<?php displayFlashedSessionValue('old', 'email') ?>" type="email">
                    <?php displayErrorMessage("email") ?>
                </div>

                <!-- File -->
                <div class="form-group">
                    <p class="form-label text-white">Profile Image</p>
                    <input type="file" class="file-input" id="profile" name="profile[]" data-multiple-caption="{count} files selected." multiple>
                    <label class="file-input-label" for="profile">
                        <div class="file-input-text-inner flex jcb aic" style="height: 100%;">
                            <div class="file-input-text">
                                Select File
                            </div>
                            <div>
                                <i class="bi bi-cloud-arrow-up-fill icon-black"></i>
                            </div>
                        </div>
                    </label>
                    <?php displayErrorMessage("profile image") ?>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label class="form-label text-white" for="password">Password</label>
                    <input class="input form-input" name="password" name="password" type="password">
                    <?php displayErrorMessage("password")  ?>
                    <?php displayAllErrorMessages() ?>
                </div>

                <!-- Login Page Link -->
                <div class="mtb-mid">
                    <a href="/login" class="btn text-d-none">
                        Already has an account?
                    </a>
                </div>

                <!--Register Button -->
                <div class="flex jcc">
                    <button class="btn" type="submit">Sign Up</button>
                </div>
            </form>
        </div>
    </div>
</body>

<!-- Nav toggle js -->
<script src="./assets/js/nav-toggle.js"></script>

<!-- File input interactive js -->
<script src="/assets/js/file-input-interactive.js"></script>

</html>