<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("../src/Views/components/head.php")  ?>

    <!-- JQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Select 2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body>
    <!-- Nav Bar -->
    <?php require_once("../src/Views/components/nav.php") ?>

    <!-- Main Body -->
    <div class="main">
        <!-- Tag Create Card -->
        <div class="card create-card">

            <!-- Header -->
            <h3 class="text-white">
                <div class="flex jcc">
                    <div>
                        <i class="bi bi-journals"></i>
                        Courses
                    </div>
                </div>
            </h3>

            <!-- Filter Section -->
            <form action="<?php echo getRouteUsingRouteName('show-public-course') ?>" method="get">

                <div class="filter-form-groups">

                    <!-- Title -->
                    <div class="filter-form-group flex aic g-sm">
                        <label class="form-label text-white" for="title">Title</label>
                        <input class="input form-input limit-input-width" id="title" name="title" value="<?php displayFlashedSessionValue('old', 'title') ?>" type="text">
                    </div>

                    <!-- Oldest -->
                    <div class="flex aic g-sm">
                        <label class="form-label text-white" for="oldest">Oldest</label>
                        <input type="checkbox" class="checkbox" name="oldest" id="oldest" <?php if (checkFlashedSessionExist('old', 'sortByOldest')) echo "checked" ?>>
                    </div>

                    <!-- Tags -->
                    <div class="flex aic g-sm">
                        <label class="form-label text-white" for="tags">Tags</label>
                        <select class="select2-multiple form-filter" id="tags" name="tags[]" multiple="multiple">
                            <?php foreach ($tags as $tag) : ?>
                                <option value="<?= htmlspecialchars($tag->getId()) ?>" <?= checkIdExistsInOldSession('tagSelected', 'selected', $tag->getId()) ?>>
                                    <?= htmlspecialchars($tag->getName()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php displayErrorMessage("tags") ?>
                    </div>
                </div>

                <div class="flex g-mid filter-action-group">
                    <button type="submit" class="btn"><i class="bi bi-search"></i> Search</button>
                    <a href="<?= getRouteUsingRouteName('show-public-course') ?>" type="submit" class="btn link-plain"><i class="bi bi-arrow-clockwise"></i> Clear</a>
                </div>
            </form>

            <!-- Courses -->
            <div class="cards">
                <?php if (count($courses) > 0) : ?>
                    <?php foreach ($courses as $course) : ?>

                        <div class="card-item text-white">
                            <a href="<?= getRouteUsingRouteName('show-public-spec-course') . "?title=" . htmlspecialchars($course->getTitle()) ?>" class="link-plain text-white">
                                <img src="<?= htmlspecialchars($course->getImage()) ?>" alt="Card Image" class="card-image">
                                <div class="card-title">
                                    <?= htmlspecialchars($course->getTitle()) ?>
                                </div>
                                <div class="card-description">
                                    <?= htmlspecialchars($course->getDescription()) ?>
                                </div>
                            </a>
                            <div class="card-description bg-pri-heavy-2 half-bottom-round-mid point-normal">
                                <?php foreach ($course->getUndeletedTags() as $tag) : ?>
                                    <a href="<?= getRouteUsingRouteName('show-public-course') . '?title=' . $course->getTitle() . "&tags%5B%5D=" . $tag->getId() ?>" class="btn link-plain text-white btn-tag-small"><?= htmlspecialchars($tag->getName()) ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="pagi-not-found">
                        <i class="bi bi-question-diamond-fill"></i> No courses found!
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination Navigation -->
            <?php
            $pagiResource = $courses;
            require_once("../src/Views/components/pagi-nav.php")
            ?>
        </div>
</body>
<!-- nav toggle -->
<script src="/assets/js/nav-toggle.js"></script>

<!-- sidebar toggle -->
<script src="/assets/js/sidebar-toggle.js"></script>

<!-- Noti Js -->
<script src="/assets/js/noti-uti.js"></script>

<!-- Multiple Select with Select 2 -->
<script>
    $(document).ready(function() {
        $('.select2-multiple').select2({});
    });
</script>


</html>