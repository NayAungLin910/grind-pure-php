<!DOCTYPE html>
<html lang="en">

<head>   
    <?php 
    $title = "Tag Edit";
    require_once("../src/Views/components/head.php");
    ?>
</head>

<body>

    <!-- Nav Bar -->
    <?php require_once("../src/Views/components/nav.php") ?>

    <!-- Sidebar --->
    <?php require_once("../src/Views/components/sidebar.php") ?>

    <!-- Main Body -->
    <main class="main sidebar-main p-mid">

        <form action="<?php echo getRouteUsingRouteName('post-tag-edit') ?>" method="POST" enctype="multipart/form-data">

            <!-- Tag Create Card -->
            <div class="card create-card">

                <!-- Header -->
                <h3 class="text-white">
                    <div class="flex jcc">
                        <div>
                            <i class="bi bi-tag"></i>
                            Tag Edit
                        </div>
                    </div>
                </h3>

                <!-- Tag Id -->
                <input type="hidden" value="<?= htmlspecialchars($tag->getId()) ?>" name="update-id">

                <!-- Title -->
                <div class="form-group">
                    <label class="form-label text-white" for="name">Name</label>
                    <input class="input form-input limit-input-width" id="name" name="name" value="<?= htmlspecialchars($tag->getName()) ?>" type="text">
                </div>
                <?php displayErrorMessage("name") ?>
                <?php displayAllErrorMessages() ?>

                <!--Submit Button -->
                <div class="flex jcc g-mid">
                    <a href="<?= getRouteUsingRouteName('show-tag') ?>" class="btn link-plain" type="submit"><i class="bi bi-arrow-left"></i> Back</a>
                    <button class="btn" type="submit">Save</button>
                </div>
            </div>
        </form>

    </main>

    <!-- Notification -->
    <?php require_once("../src/Views/components/notification.php") ?>
</body>

<!-- nav toggle -->
<script src="/assets/js/nav-toggle.js"></script>

<!-- sidebar toggle -->
<script src="/assets/js/sidebar-toggle.js"></script>

<!-- File input interactive js -->
<script src="/assets/js/file-input-interactive.js"></script>

<!-- Noti Js -->
<script src="/assets/js/noti-uti.js"></script>

</html>