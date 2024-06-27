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

        <form id="switch-form-parent" action="<?php echo getRouteUsingRouteName('post-step-create') ?>" method="POST" enctype="multipart/form-data">

            <!-- Course Create Card -->
            <div class="card create-card">

                <!-- Header -->
                <h3 class="text-white">
                    <div class="flex jcc">
                        <div>
                            <i class="bi bi-journals"></i>
                            Create Step for Section, <?= $section->getId() ?> of the Course, <?= $section->getCourse()->getTitle() ?>
                        </div>
                    </div>
                </h3>

                <!-- Section Id -->
                <input type="hidden" value="<?= $section->getId() ?>" name="section-id">

                <!-- Type -->
                <input type="hidden" id="type-form" name="type" class="form-type" value="video">

                <div class="flex jcc g-mid form-group aic">
                    <p class="text-mid">Choose Step Type: </p>
                    <button class="btn form-switch-button" type="button" id="video-form-button" onclick="switchForm('video-form', 'video-form-button')">Video</button>
                    <button class="btn form-switch-button" type="button" id="reading-form-button" onclick="switchForm('reading-form', 'reading-form-button')">Reading</button>
                    <button class="btn form-switch-button" type="button" id="quiz-form-button" onclick="switchForm('quiz-form', 'quiz-form-button')">Quiz</button>
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

                <!-- Reading Form -->
                <div class="reading-form switch-form">

                    <!-- Reading Context -->
                    <div class="form-group">
                        <label class="form-label text-white" for="reading-context">Reading Context</label>
                        <textarea name="reading-context" rows="8" cols="30" class="textarea form-input" id="reading-context"><?php displayFlashedSessionValue('old', 'reading context') ?></textarea>
                        <?php displayErrorMessage("reading context") ?>
                    </div>
                </div>

                <!-- Video Form -->
                <div class="video-form switch-form ">
                    <!-- Course Image -->
                    <div class="form-group">
                        <p class="form-label text-white">Video</p>
                        <input type="file" class="file-input limit-input-width" id="video" name="video" data-multiple-caption="{count} files selected.">
                        <label class="file-input-label limit-input-width" for="video">
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
                    <?php displayErrorMessage("video") ?>
                </div>

                <!-- Quiz Form -->
                <div class="quiz-form switch-form ">
                    <div class="flex jcc">
                        <div class="warning-text">
                            <i class="bi bi-exclamation-diamond"></i> Note: The questions of the quiz can be added later in the edit page.
                        </div>
                    </div>
                </div>

                <!-- Priority -->
                <div class="form-group">
                    <label class="form-label text-white" for="priority">Priority</label>
                    <select class="input form-input form-select limit-input-width-xs square" id="priority" name="priority">

                        <?php if (count($section->getSteps()) > 0) : ?>
                            <?php foreach ($section->getSteps() as $step) : ?>
                                <option value="<?= htmlspecialchars($step->getPriority()) ?>"><?= htmlspecialchars($step->getPriority()) ?></option>
                            <?php endforeach; ?>

                            <!-- Latest priority -->
                            <option value="<?= htmlspecialchars($section->getSteps()->last()->getPriority() + 1) ?>" selected>
                                <?= htmlspecialchars($step->getSteps()->last()->getPriority() + 1) ?>
                            </option>
                        <?php else : ?>
                            <!-- Latest priority -->
                            <option value="1" selected>
                                1
                            </option>
                        <?php endif; ?>
                    </select>
                    <p>
                        <?php displayErrorMessage("priority") ?>
                        <?php displayAllErrorMessages() ?>
                    </p>

                    <!--Submit Button -->
                    <div class="flex jcc form-group">
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

<!-- Form Switch -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>;
<script src="/assets/js/form-switch.js"></script>
<!-- Initialize button click -->
<script>
    // trigger an initial click on page load

    let type = "<?= isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'video' ?>"

    if (type === 'video' || type === 'reading' || type === 'quiz') {
        let firstSwitchButton = document.querySelector(`#${type}-form-button`);
        firstSwitchButton.click();
    }
</script>

</html>