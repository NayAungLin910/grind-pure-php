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

    <!-- Main Body -->
    <div class="main">
        <div class="p-mid sm-f-column">

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

            <div class="p-mid mtb-mid card create-card">
                <!-- Filter Section -->
                <form action="<?php echo getRouteUsingRouteName('profile') ?>" method="get">

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

                        <!-- Completed -->
                        <div class="flex aic g-sm">
                            <label class="form-label text-white" for="completed">Completed</label>
                            <input type="checkbox" class="checkbox" name="completed" id="completed" <?php if (checkFlashedSessionExist('old', 'completed')) echo "checked" ?>>
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
                        </div>


                    </div>

                    <div class="flex g-mid filter-action-group">
                        <button type="submit" class="btn"><i class="bi bi-search"></i> Search</button>
                        <a href="<?= getRouteUsingRouteName('profile') ?>" type="submit" class="btn link-plain"><i class="bi bi-arrow-clockwise"></i> Clear</a>
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
                                    <div class="mtb-sm">
                                        <?php if ($course->getEnrollments() && count($course->getEnrollments()) > 0) : ?>
                                            <?php foreach ($course->getEnrollments() as $enrollment) : ?>
                                                <?php if ($enrollment->getUserId() === $_SESSION['auth']['id'] && $enrollment->getStatus() === 'completed') : ?>
                                                    <div class="flex mtb-sm jcc g-mid">
                                                        <div class="badge-success">Completed</div>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </a>
                                <div class="card-description bg-pri-heavy-2 half-bottom-round-mid point-normal">
                                    <?php foreach ($course->getUndeletedTags() as $tag) : ?>
                                        <a href="<?= getRouteUsingRouteName('show-public-course') . '?title=' . $course->getTitle() . "&tags%5B%5D=" . $tag->getId() ?>" class="btn link-plain text-white btn-tag-small"><?= htmlspecialchars($tag->getName()) ?></a>
                                    <?php endforeach; ?>
                                    <?php if ($course->getEnrollments() && count($course->getEnrollments()) > 0) : ?>
                                        <?php foreach ($course->getEnrollments() as $enrollment) : ?>
                                            <?php if ($enrollment->getUserId() === $_SESSION['auth']['id'] && $enrollment->getStatus() === 'completed') : ?>
                                                <a href="<?= getRouteUsingRouteName('get-cert-download') . '?course-id=' . $course->getId() ?>" target="_blank" class="badge-warning link-plain text-black">Certificate</a>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
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

<!-- Multiple Select with Select 2 -->
<script>
    $(document).ready(function() {
        $('.select2-multiple').select2({});
    });
</script>

</html>