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

            <form action="/register" method="POST">

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

                <!-- Password -->
                <div class="form-group">
                    <label class="form-label text-white" for="password">Password</label>
                    <input class="input form-input" name="password" name="password" type="password">
                    <?php displayErrorMessage("password") ?>
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

</html>