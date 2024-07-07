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

        <div class="course-info">

            <!-- Section Create/Edit -->
            <div class="course-info-form">

                <?php if ($section && isset($_GET['edit-section-id'])) : ?>
                    <!-- Edit Section --->
                    <div class="course-info-form-card">
                        <h3 class=""><i class="bi bi-list"></i>Edit Section <?= htmlspecialchars($section->getTitle()) ?></h3>

                        <form action="<?= getRouteUsingRouteName('post-section-edit') ?>" method="POST" enctype="multipart/form-datap">

                            <!-- Section Id -->
                            <div class="form-group">
                                <input type="hidden" name="section-id" value="<?= htmlspecialchars($section->getId()) ?>" />
                            </div>

                            <!-- Course Id --->
                            <div class="form-group">
                                <input type="hidden" name="course-id" value="<?= htmlspecialchars($course->getId()) ?>">
                            </div>

                            <!-- Edit Section Title -->
                            <div class="form-group">
                                <label class="form-label text-white" for="edit-section-title">Title</label>
                                <input class="input form-input limit-input-width" id="edit-section-title" name="edit-section-title" value="<?= htmlspecialchars($section->getTitle()) ?>" type="text">
                            </div>
                            <?php displayErrorMessage("edit-section-title") ?>

                            <!-- Edit Section Description -->
                            <div class="form-group">
                                <label class="form-label text-white" for="edit-section-description">Description</label>
                                <textarea name="edit-section-description" rows="8" cols="30" class="textarea form-input" id="edit-section-description"><?= htmlspecialchars($section->getDescription()) ?></textarea>
                                <?php displayErrorMessage("edit-section-description") ?>
                            </div>

                            <!-- Priority -->
                            <div class="form-group">
                                <label class="form-label text-white" for="priority">Priority</label>
                                <select class="input form-input form-select limit-input-width-xs square" id="priority" name="priority">
                                    <?php foreach ($course->getSections() as $s) : ?>
                                        <option value="<?= htmlspecialchars($s->getPriority()) ?>" <?php if ($section->getPriority() === $s->getPriority()) echo "selected" ?>>
                                            <?= htmlspecialchars($course->getSections()->indexOf($s) + 1) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php displayErrorMessage("priority") ?>
                            </div>
                            <?php displayAllErrorMessages() ?>

                            <!--Submit Button -->
                            <div class="flex jcc g-mid">
                                <a href="<?= getRouteUsingRouteName("show-single-course") . "?title=" . htmlspecialchars($course->getTitle()) ?>" class="btn link-plain">Cancel</a>
                                <button class="btn" type="submit">Update</button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>

                <!-- Create Section -->
                <div class="course-info-form-card">
                    <h3 class=""><i class="bi bi-list"></i>Create Section</h3>

                    <form action="<?= getRouteUsingRouteName('post-section-create') ?>" method="POST" enctype="multipart/form-datap">

                        <!-- Course ID -->
                        <div class="form-group">
                            <input type="hidden" name="course-id" value="<?= htmlspecialchars($course->getId()) ?>" />
                        </div>

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

                        <!-- Priority -->
                        <div class="form-group">
                            <label class="form-label text-white" for="priority">Priority</label>
                            <select class="input form-input form-select limit-input-width-xs square" id="priority" name="priority">

                                <?php if (count($course->getSections()) > 0) : ?>
                                    <?php foreach ($course->getSections() as $section) : ?>
                                        <option value="<?= htmlspecialchars($section->getPriority()) ?>"><?= htmlspecialchars($course->getSections()->indexOf($section) + 1) ?></option>
                                    <?php endforeach; ?>

                                    <!-- Latest priority -->
                                    <option value="<?= htmlspecialchars($course->getSections()->last()->getPriority() + 1) ?>" selected>
                                        <?= htmlspecialchars($course->getSections()->last()->getPriority() + 1) ?>
                                    </option>
                                <?php else : ?>
                                    <!-- Latest priority -->
                                    <option value="1" selected>
                                        1
                                    </option>
                                <?php endif; ?>
                            </select>
                            <?php displayErrorMessage("priority") ?>
                        </div>
                        <?php displayAllErrorMessages() ?>

                        <!--Submit Button -->
                        <div class="flex jcc">
                            <button class="btn" type="submit">Create</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Course Info -->
            <div class="course-info-course">
                <div class="course-info-course-card">
                    <div class="course-info-img">
                        <img src="<?= htmlspecialchars($course->getImage()) ?>" alt="<?= htmlspecialchars($course->getTitle()) . "'s image" ?>">
                    </div>
                    <div class="course-info-text">
                        <div class="course-info-title"><?= htmlspecialchars($course->getTitle()) ?></div>
                        <div class="course-info-description">
                            <?= htmlspecialchars($course->getDescription()) ?>
                        </div>
                        <div class="form-group flex g-mid">

                            <!-- Edit Course -->
                            <a href="<?= getRouteUsingRouteName('show-course-edit') . "?title=" . htmlspecialchars($course->getTitle()) ?>" class="btn btn-sm square">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <!-- Delete Course -->
                            <form method="POST" id="delete-form-course-<?= $course->getId() ?>" action="<?= getRouteUsingRouteName('post-course-bin') ?>">
                                <!-- Course Id -->
                                <input type="hidden" name="course-id" value="<?= $course->getId() ?>">

                                <button type="submit" class="btn square">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </div>
                        <?php if (count($course->getUndeletedTags()) > 0) : ?>
                            <div class="card-description">

                                <!-- Tags -->
                                <?php foreach ($course->getUndeletedTags() as $tag) : ?>
                                    <a href="<?= getRouteUsingRouteName('show-public-course') . "?title=" . $course->getTitle() . "&tags%5B%5D=" . $tag->getId() ?>" class="btn link-plain text-white btn-tag-small"><?= htmlspecialchars($tag->getName()) ?></a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <div class="course-info-sections">

                            <!-- Sections -->
                            <?php foreach ($course->getSections() as $section) : ?>
                                <div class="section-group">
                                    <div class="course-info-section" onclick="dropdownToggle(<?= htmlspecialchars($section->getId()) ?>)">
                                        <div>
                                            <?= htmlspecialchars($section->getTitle()) ?>
                                        </div>

                                        <i class="bi bi-chevron-down drop-arrow" id="drop-arrow-<?= htmlspecialchars($section->getId()) ?>"></i>
                                    </div>
                                    <div class="course-info-section-steps drop-sub-menu" id="drop-sub-menu-<?= htmlspecialchars($section->getId()) ?>">
                                        <div class="mtb-sm">
                                            <?= htmlspecialchars($section->getDescription()) ?>
                                        </div>
                                        <div class="flex jcc g-mid">
                                            <a href="<?= getRouteUsingRouteName('show-step-create') . "?section-id=" . htmlspecialchars($section->getId()) . "&type=video" ?>" class="btn btn-small link-plain">Add Step</a>
                                            <a href="<?= getRouteUsingRouteName('show-single-course') . "?edit-section-id=" . htmlspecialchars($section->getId()) . "&title=" . htmlspecialchars($course->getTitle()) ?>" class="btn btn-small link-plain square">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <!-- Delete section -->
                                            <form action="<?= getRouteUsingRouteName('post-section-delete') ?>" id="delete-form-<?= $section->getId() ?>" method="POST">
                                                <input type="hidden" name="delete-id" value="<?= $section->getId() ?>" />
                                                <button type="button" class="btn btn-small square btn-light" onclick="confirmDelete(<?= $section->getId() ?>, '<?= $section->getTitle() ?>', 'course')">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        </div>

                                        <!-- Steps -->
                                        <?php if (count($section->getSteps()) > 0) : ?>
                                            <div class="step-section">
                                                <?php foreach ($section->getSteps() as $st) : ?>
                                                    <div class="flex jcb aic step-div">
                                                        <p class="step-link"><?= $st->getTitle() ?></p>
                                                        <div class="flex jce g-mid">

                                                            <!-- Edit Step Btn -->
                                                            <a href="<?= getRouteUsingRouteName('show-step-edit') . "?edit-id=" . $st->getId() ?>" class="btn btn-small link-plain square">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </a>

                                                            <!-- Delete Step Btn -->
                                                            <form action="<?= getRouteUsingRouteName('post-step-delete') ?>" id="delete-form-step-<?= $st->getId() ?>" method="POST">
                                                                <input type="hidden" value="<?= $st->getId() ?>" name="delete-id">
                                                                <button type="button" onclick="confirmDelete('<?= 'step-' . $st->getId() ?>', '<?= $st->getTitle() ?>', 'step')" class="btn btn-small square">
                                                                    <i class="bi bi-trash-fill"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- Notification -->
    <?php require_once("../src/Views/components/notification.php") ?>
</body>

<!-- nav toggle -->
<script src="/assets/js/nav-toggle.js"></script>

<!-- sidebar toggle -->
<script src="/assets/js/sidebar-toggle.js"></script>

<!-- Noti Js -->
<script src="/assets/js/noti-uti.js"></script>

<!-- Utilities -->
<script src="/assets/js/utilities.js"></script>

<!-- Sweetalert 2 Js -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Sweetalert Confirm Delete -->
<script src="/assets/js/sweet-alert-utilities.js"></script>

</html>