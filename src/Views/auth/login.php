<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Head -->
    <?php require_once("../src/Views/components/head.php")  ?>
    
    <title>Grind | Login</title>
</head>

<body>

    <!-- Nav -->
    <?php require_once("../src/Views/components/nav.php") ?>

    <!-- Main -->
    <div class="main">

        <!-- Login Card -->
        <div class="card login-card">

            <form action="/login" method="POST">

                <!-- Card Header -->
                <h3 class="card-header">Login</h3>

                <!-- Email -->
                <div class="form-group">
                    <label class="form-label text-white" for="email">Email</label>
                    <input class="input form-input" id="email" name="email" value="<?php  ?>" type="email">
                    <?php displayErrorMessage("email") ?>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label class="form-label text-white" for="password">Password</label>
                    <input class="input form-input" name="password" name="password" type="password">
                    <?php displayErrorMessage("password") ?>
                    <?php displayAllErrorMessages() ?>
                </div>

                <!-- Register Page Link -->
                <div class="mtb-mid">
                    <a href="/register" class="btn text-d-none">
                        Sign up a new acccount?
                    </a>
                </div>

                <!-- Submit Button -->
                <div class="flex jcc">
                    <button class="btn" type="submit">Login</button>
                </div>
            </form>
        </div>
    </div>
</body>

<script src="./assets/js/nav-toggle.js"></script>

</html>