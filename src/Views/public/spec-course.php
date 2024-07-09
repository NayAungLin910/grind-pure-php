<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("../src/Views/components/head.php")  ?>

    <!-- VideoJS -->
    <link href="https://vjs.zencdn.net/8.12.0/video-js.css" rel="stylesheet" />
    <script src="https://vjs.zencdn.net/8.12.0/video.min.js"></script>
</head>

<body>

    <!-- Nav Bar -->
    <?php require_once("../src/Views/components/nav.php") ?>

    <!-- Main Body -->
    <main class="main p-mid">

        <div class="course-info">

            <!-- Course Details -->
            <div class="course-info-form">
                <div class="text-white card-round p-mid bg-pri-heavy ">

                    <?php if (!$ifAuth || !$course->checkEnrolled(getAuthUser())) : ?>
                        <div class="flex jcc">
                            <div class="warning-text">
                                <i class="bi bi-exclamation-diamond"></i> Please enroll the course to see the details.
                            </div>
                        </div>
                        <div class="flex jcc">
                            <form method="POST" action="<?= getRouteUsingRouteName('post-course-enroll') ?>">
                                <input type="hidden" value="<?= $course->getId() ?>" name="course-id">
                                <button type="submit" class="btn">Enroll</button>
                            </form>
                        </div>
                    <?php else : ?>

                        <h3 class="text-center text-big mtb-sm text-bold"><?= $currentStep->getTitle() ?></h3>

                        <!-- Video Step -->
                        <?php if ($currentStep->getType() === 'video') : ?>
                            <video id="my-video" class="video-js  vjs-16-9" controls preload="auto" data-setup="{}">
                                <source src="/range-request-handler.php?path=<?= $currentStep->getVideo() ?>" type="video/mp4" />
                                <!-- <source src="MY_VIDEO.webm" type="video/webm" /> -->
                                <p class="vjs-no-js">
                                    To view this video please enable JavaScript, and consider upgrading to a
                                    web browser that
                                    <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                                </p>
                            </video>
                        <?php endif; ?>

                        <!-- Reading Content -->
                        <?php if ($currentStep->getType() === 'reading') : ?>
                            <p class="mtb-mid">
                                <?= htmlspecialchars($currentStep->getReadingContent()) ?>
                            </p>
                        <?php endif; ?>

                        <p class="mtb-mid">
                            <?= htmlspecialchars($currentStep->getDescription()) ?>
                        </p>

                        <!-- Quiz Step -->
                        <?php if ($currentStep->getType() === 'quiz') : ?>
                            <?php if ($currentStep->getQuestions() && count($currentStep->getQuestions()) > 0) : ?>
                                <div class="res-table">
                                    <form action="<?= getRouteUsingRouteName('post-quiz-answer') ?>" id="quiz-submit-form" method="post">
                                        <input type="hidden" name="step-id" value="<?= $currentStep->getId() ?>">
                                        <table class="width-auto table-no-border">
                                            <?php foreach ($currentStep->getQuestions() as $question) : ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($currentStep->getQuestions()->indexOf($question) + 1) ?>.</td>
                                                    <td>
                                                        <div class="question-row"><?= htmlspecialchars($question->getDescription()) ?></div>
                                                    </td>
                                                    <td>
                                                    </td>
                                                    <td></td>
                                                </tr>

                                                <!-- Answers -->
                                                <?php if ($question->getAnswers() || count($question->getAnswers()) > 0) : ?>
                                                    <tr>
                                                        <td></td>
                                                        <td colspan="2">
                                                            <table class="width-auto text-normal table-no-border">
                                                                <?php foreach ($question->getAnswers() as $answer) : ?>
                                                                    <tr>
                                                                        <td>
                                                                            <?= htmlspecialchars($question->getAnswers()->indexOf($answer) + 1 . ".") ?>
                                                                        </td>
                                                                        <td>
                                                                            <?= htmlspecialchars($answer->getDescription()) ?>
                                                                        </td>
                                                                        <td>
                                                                            <input type="radio" name="question-<?= $question->getId() ?>" value="<?= $answer->getId() ?>">
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="3">
                                                                            <?php displaySuccessMessage("question-" . $question->getId()) ?>
                                                                            <?php displayErrorMessage("question-" . $question->getId() . "-error") ?>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>

                                                            </table>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                                <tr>
                                                    <td colspan="3">
                                                        <?php displayErrorMessage("question-error-" . $question->getId()) ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <tr>
                                                <td colspan="3">
                                                    <?php displaySuccessMessage('result') ?>
                                                    <?php displayErrorMessage("result-error") ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($currentStep->getType() === 'reading' || $currentStep->getType() === 'video') : ?>
                            <?php if (!$currentStep->checkCompleted($_SESSION['auth']['id'])) : ?>
                                <!-- Complete Button -->
                                <form action="<?= getRouteUsingRouteName('post-step-complete') ?>" method="POST">
                                    <p class="flex jcc">
                                        <input type="hidden" name="current-step-id" value="<?= $currentStep->getId() ?>">
                                        <button type="submit" class="btn bg-pri-heavy-2">Compelete</button>
                                    </p>
                                </form>
                            <?php else : ?>
                                <div class="flex jcc">
                                    <div class="badge-success">Completed</div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($currentStep->getType() === 'quiz') : ?>
                            <div class="flex jcc">
                                <!-- Submit Button -->
                                <button type="submit" form="quiz-submit-form" class="btn bg-pri-heavy-2">Submit</button>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
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

                        <?php if (count($course->getUndeletedTags()) > 0) : ?>
                            <div class="card-description">

                                <!-- Tags -->
                                <?php foreach ($course->getUndeletedTags() as $tag) : ?>
                                    <a href="" class="btn link-plain text-white btn-tag-small"><?= htmlspecialchars($tag->getName()) ?></a>
                                <?php endforeach; ?>

                            </div>
                        <?php endif; ?>

                        <?php foreach ($course->getEnrollments() as $enroll) : ?>
                            <?php if ($enroll->getUser()->getId() === $_SESSION['auth']['id'] && $enroll->getStatus() === 'completed') : ?>
                                <div class="flex mtb-sm jcc">
                                    <div class="badge-success">Completed</div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <?php checkCourseCompleted($course) ?>

                        <?php if (($ifAuth) && $course->checkEnrolled(getAuthUser())) : ?>
                            <div class="course-info-sections">

                                <!-- Sections -->
                                <?php foreach ($course->getSections() as $section) : ?>
                                    <div class="section-group">
                                        <div class="course-info-section" onclick="dropdownToggle(<?= htmlspecialchars($section->getId()) ?>)">
                                            <div>
                                                <?= htmlspecialchars($section->getTitle()) ?>
                                            </div>

                                            <i class="bi bi-chevron-down drop-arrow <?= $section->checkContainsStep($currentStep->getId()) ? 'rotate' : '' ?>" id="drop-arrow-<?= htmlspecialchars($section->getId()) ?>"></i>
                                        </div>
                                        <div class="course-info-section-steps drop-sub-menu <?= $section->checkContainsStep($currentStep->getId()) ? 'display' : '' ?>" id="drop-sub-menu-<?= htmlspecialchars($section->getId()) ?>">
                                            <div class="mtb-sm">
                                                <?= htmlspecialchars($section->getDescription()) ?>
                                            </div>

                                            <!-- Steps -->
                                            <?php if (count($section->getSteps()) > 0) : ?>
                                                <div class="step-section">
                                                    <?php foreach ($section->getSteps() as $st) : ?>
                                                        <a class="link-plain text-white" href="<?= getRouteUsingRouteName('show-public-spec-course') . "?title=" . $course->getTitle() . "&current-step=" . $st->getId() ?>">
                                                            <div class="flex jcb aic p-mid step-div <?= $st->getId() == $currentStep->getId() ? 'active' : '' ?>">
                                                                <p class="step-link"><?=
                                                                                        $st->getTitle() ?></p>

                                                                <?php if ($st->checkCompleted($_SESSION['auth']['id'])) : ?>
                                                                    <i class="bi bi-check-circle text-successp"></i>
                                                                <?php endif; ?>
                                                            </div>
                                                        </a>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>

                                        </div>
                                    </div>
                                <?php endforeach; ?>

                            </div>
                        <?php endif; ?>

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

<!-- Noti Js -->
<script src="/assets/js/noti-uti.js"></script>

<!-- Utilities -->
<script src="/assets/js/utilities.js"></script>

<!-- Sweetalert 2 Js -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Sweetalert Confirm Delete -->
<script src="/assets/js/sweet-alert-utilities.js"></script>

<!-- Video Js  -->
<?php if ($currentStep->getType() === 'video') : ?>
    <script>
        let player = videojs('my-video', {
            // autoplay: true,
            controls: true,
            fluid: true
        });
    </script>
<?php endif; ?>

</html>