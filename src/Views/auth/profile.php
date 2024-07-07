<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("../src/Views/components/head.php")  ?>
</head>

<body>
    <!-- Nav Bar -->
    <?php require_once("../src/Views/components/nav.php") ?>

    <!-- Main Body -->
    <div class="main">
        <div class="flex jcc g-mid p-mid sm-f-column">

            <!-- Profile -->
            <div class="profile-card">

                <!-- Current Profile -->
                <div class="flex jcc">
                    <img src="<?= $_SESSION['auth']['profile_image'] ?>" class="profile-img-lg" alt="">
                </div>

                <form method="POST" action="<?= getRouteUsingRouteName('post-profile') ?>" enctype="multipart/form-data">
                    <!-- Name -->
                    <div class="form-group">
                        <label class="form-label text-white" for="name">Name</label>
                        <input class="input form-input" id="name" name="name" value="<?= $_SESSION['auth']['name'] ?>" type="name">
                        <?php displayErrorMessage("name") ?>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-label text-white" for="email">Email</label>
                        <input class="input form-input" id="email" name="email" value="<?= $_SESSION['auth']['email'] ?>" type="email">
                        <?php displayErrorMessage("email") ?>
                    </div>

                    <!-- Profile Image -->
                    <div class="form-group">
                        <p class="form-label text-white">Profile Image</p>
                        <input type="file" class="file-input" id="profile" name="profile" data-multiple-caption="{count} files selected." multiple>
                        <label class="file-input-label text-black" for="profile">
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

                    <!--Register Button -->
                    <div class="flex jcc">
                        <button class="btn" type="submit">Save</button>
                    </div>
                </form>

                <form method="POST" action="<?= getRouteUsingRouteName('post-password-change') ?>" enctype="multipart/form-data">

                <h3 class="mtb-mid">Change Password</h3>

                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label text-white" for="current-password">Current-password</label>
                        <input class="input form-input" id="current-password" name="current-password" type="password">
                        <?php displayErrorMessage("current-password") ?>
                    </div>

                    <!-- New Password -->
                    <div class="form-group">
                        <label class="form-label text-white" for="new-password">New Password</label>
                        <input class="input form-input" id="new-password" name="new-password" type="password">
                        <?php displayErrorMessage("new-password") ?>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label class="form-label text-white" for="confirm-password">Confirm Password</label>
                        <input class="input form-input" id="confirm-password" name="confirm-password" type="password">
                        <?php displayErrorMessage("confirm-password") ?>
                    </div>

                    <!--Register Button -->
                    <div class="flex jcc">
                        <button class="btn" type="submit">Change</button>
                    </div>
                </form>
            </div>

            <!-- Nahhh I' woudld adapt -->
            <div>
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Blanditiis, architecto? Laboriosam ratione perferendis sunt quia vitae minus, et illo cumque ullam expedita. Totam ducimus cum autem expedita fugit doloremque sunt.
            </div>

            <!--Change Password -->

        </div>
    </div>

    <!-- Notification -->
    <?php require_once("../src/Views/components/notification.php") ?>
</body>

<!-- Nav Toggle -->
<script src="./assets/js/nav-toggle.js"></script>

<!-- File Input Interactive -->
<script src="./assets/js/file-input-interactive.js"></script>

<!-- Noti Js -->
<script src="/assets/js/noti-uti.js"></script>

</html>