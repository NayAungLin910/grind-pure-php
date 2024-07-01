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
        <!-- Course Create Card -->
        <div class="card create-card">

            <form id="switch-form-parent" action="<?= getRouteUsingRouteName('post-step-edit') ?>" method="POST" enctype="multipart/form-data">
                <!-- Header -->
                <h3 class="text-white">
                    <div class="flex jcc">
                        <div>
                            <i class="bi bi-journals"></i>
                            Edit Step for Section, <?= htmlspecialchars($step->getSection()->getTitle()) ?> of the Course, <?= htmlspecialchars($step->getSection()->getCourse()->getTitle()) ?>
                        </div>
                    </div>
                </h3>

                <!-- Step Id -->
                <input type="hidden" value="<?= htmlspecialchars($step->getId()) ?>" name="step-id">

                <!-- Type -->
                <input type="hidden" value="<?= htmlspecialchars($step->getType()) ?>" name="type">

                <!-- Title -->
                <div class="form-group">
                    <label class="form-label text-white" for="title">Title</label>
                    <input class="input form-input limit-input-width" id="title" name="title" value="<?= htmlspecialchars($step->getTitle()) ?>" type="text">
                </div>
                <?php displayErrorMessage("title") ?>

                <!-- Description -->
                <div class="form-group">
                    <label class="form-label text-white" for="description">Description</label>
                    <textarea name="description" rows="8" cols="30" class="textarea form-input" id="description"><?= htmlspecialchars($step->getDescription()) ?></textarea>
                    <?php displayErrorMessage("description") ?>
                </div>

                <?php if ($step->getType() === 'reading') : ?>
                    <!-- Reading Form -->
                    <div>
                        <!-- Reading Context -->
                        <div class="form-group">
                            <label class="form-label text-white" for="reading-context">Reading Context</label>
                            <textarea name="reading-context" rows="8" cols="30" class="textarea form-input" id="reading-context"><?= htmlspecialchars($step->getReadingContent()) ?></textarea>
                            <?php displayErrorMessage("reading context") ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($step->getType() === 'video') : ?>
                    <!-- Video Form -->
                    <div>
                        <!-- Course Image -->
                        <div class="form-group">
                            <p class="form-label text-white">New Video</p>
                            <input type="file" class="file-input limit-input-width" id="video" name="video" data-multiple-caption="{count} files selected.">
                            <label class="file-input-label limit-input-width" for="video">
                                <div class="file-input-text-inner flex jcb aic text-black" style="height: 100%;">
                                    <div class="file-input-text">
                                        Select New Video
                                    </div>
                                    <div>
                                        <i class="bi bi-cloud-arrow-up-fill icon-black"></i>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <?php displayErrorMessage("video") ?>
                    </div>
                <?php endif; ?>

                <!-- Priority -->
                <div class="form-group">
                    <label class="form-label text-white" for="priority">Priority</label>
                    <select class="input form-input form-select limit-input-width-xs square" id="priority" name="priority">

                        <?php if (count($step->getSection()->getSteps()) > 0) : ?>
                            <?php foreach ($step->getSection()->getSteps() as $st) : ?>
                                <option value="<?= htmlspecialchars($st->getPriority()) ?>" <?= $st->getId() === $step->getId() ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($step->getSection()->getSteps()->indexOf($st) + 1) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <p>
                        <?php displayErrorMessage("priority") ?>
                        <?php displayAllErrorMessages() ?>
                    </p>

                    <div class="flex jcc form-group g-mid">
                        <!-- Cancel Button -->
                        <a class="btn link-plain" href="<?= getRouteUsingRouteName('show-single-course') . "?title=" . htmlspecialchars($step->getSection()->getCourse()->getTitle()) ?>">Back</a>

                        <!--Submit Button -->
                        <input name="submit-edit-step" class="btn" type="submit" value="Submit" />
                    </div>

                </div>
            </form>

            <?php if ($step->getType() === 'quiz') : ?>

                <!-- Questions -->
                <label class="form-label text-white">Questions</label>
                <?php if (count($step->getQuestions()) > 0) : ?>
                    <?php foreach ($step->getQuestions() as $question) : ?>
                        <div class="flex g-mid aic">
                            <span><?= htmlspecialchars($step->getQuestions()->indexOf($question) + 1) ?>.</span>
                            <div class="question-row"><?= htmlspecialchars($question->getDescription()) ?></div>
                            <a href="<?= getRouteUsingRouteName('show-step-edit') . "?edit-id=" . htmlspecialchars($step->getId()) . "&question-edit=" . htmlspecialchars($question->getId()) ?>" class="btn btn-small square">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>


                <!-- Quiz Form -->
                <div>

                    <?php if (!isset($_GET['question-edit'])) : ?>
                        <!-- New Question -->
                        <form action="<?= getRouteUsingRouteName('post-question-create') ?>" method="POST">
                            <input type="hidden" name="step-id" value="<?= $step->getId() ?>">
                            <div class="form-group">
                                <label class="form-label text-white" for="title">New Question</label>
                                <input class="input form-input limit-input-width" id="question" name="question" value="<?= displayFlashedSessionValue('old', 'question') ?>" type="text">
                            </div>
                            <?php displayErrorMessage("question") ?>
                            <div>
                                <button type="submit" class="btn">Add</button>
                            </div>
                        </form>
                    <?php else : ?>
                        <form action="<?= getRouteUsingRouteName('post-question-edit') ?>" method="POST">
                            <input type="hidden" name="step-id" value="<?= $step->getId() ?>">
                            <!-- Edit Question -->
                            <div class="form-group">
                                <label class="form-label text-white" for="title">Edit Question</label>
                                <input type="hidden" name="question-id" value="<?= $questionEdit->getId() ?>">
                                <input class="input form-input limit-input-width" id="question" name="question" value="<?= htmlspecialchars($questionEdit->getDescription()) ?>" type="text">
                            </div>
                            <?php displayErrorMessage("question") ?>
                            <div class="flex g-mid">
                                <a class="btn link-plain" href="<?= getRouteUsingRouteName('show-step-edit') . "?edit-id=" . $step->getId() ?>">Back</a>
                                <button type="submit" class="btn">Save</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
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