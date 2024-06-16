<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("../src/Views/components/head.php")  ?>
</head>

<body>

    <!-- Nav Bar -->
    <?php require_once("../src/Views/components/nav.php") ?>

    <!-- Sidebar --->
    <?php require_once("../src/Views/components/sidebar.php") ?>

    <!-- Main Body -->
    <main class="main sidebar-main p-mid">

        <form action="<?php echo getRouteUsingRouteName('post-course-create') ?>" method="POST" enctype="multipart/form-data">

            <!-- Course Create Card -->
            <div class="card create-card">

                <!-- Header -->
                <h3 class="text-white">
                    <div class="flex jcc">
                        <div>
                            <i class="bi bi-journals"></i>
                            Course
                        </div>
                    </div>
                </h3>

                <!-- Title -->
                <div class="form-group">
                    <label class="form-label text-white" for="title">Title</label>
                    <input class="input form-input limit-input-width" id="title" name="title" value="<?php displayFlashedSessionValue('old', 'title') ?>" type="text">
                </div>
                <?php displayErrorMessage("title") ?>

                <!-- Description -->
                <div class="form-group">
                    <label class="form-label text-white" for="description">Description</label>
                    <textarea name="description" rows="8" cols="30" class="textarea form-input" id="description"><?php displayFlashedSessionValue('old', 'description') ?></textarea>
                    <?php displayErrorMessage("description") ?>
                </div>

                <!-- Course Image -->
                <div class="form-group">
                    <p class="form-label text-white">Course Image</p>
                    <input type="file" class="file-input limit-input-width" id="image" name="image" data-multiple-caption="{count} files selected." multiple>
                    <label class="file-input-label limit-input-width" for="image">
                        <div class="file-input-text-inner flex jcb aic text-black" style="height: 100%;">
                            <div class="file-input-text">
                                Select File
                            </div>
                            <div>
                                <i class="bi bi-cloud-arrow-up-fill icon-black"></i>
                            </div>
                        </div>
                    </label>
                </div>
                <?php displayErrorMessage("image") ?>
                <?php displayAllErrorMessages() ?>

                <!--Submit Button -->
                <div class="flex jcc">
                    <button class="btn" type="submit">Create</button>
                </div>
            </div>
        </form>

    </main>
</body>

<!-- nav toggle -->
<script src="/assets/js/nav-toggle.js"></script>

<!-- sidebar toggle -->
<script src="/assets/js/sidebar-toggle.js"></script>

<!-- File input interactive js -->
<script src="/assets/js/file-input-interactive.js"></script>

</html>