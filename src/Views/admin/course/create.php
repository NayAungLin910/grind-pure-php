<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("../src/Views/components/head.php") ?>

    <!-- JQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Select 2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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

                <div class="form-group">
                    <label class="form-label text-white" for="tags">Tags</label>
                    <select class="select2-multiple form-select limit-input-width" id="tags" name="tags[]" multiple="multiple">
                        <?php foreach ($tags as $tag) : ?>
                            <option value="<?= $tag->getId() ?>" <?= checkIdExistsInOldSession('tags', 'selected', $tag->getId()) ?>><?= $tag->getName() ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php displayErrorMessage("tags") ?>
                <?php displayAllErrorMessages() ?>

                <!--Submit Button -->
                <div class="flex jcc">
                    <button class="btn" type="submit">Create</button>
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

<!-- Multiple Select with Select 2 -->
<script>
    $(document).ready(function() {
        $('.select2-multiple').select2({
            theme: 'classic',
        });
    });
</script>

</html>